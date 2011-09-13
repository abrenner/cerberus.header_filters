<?php
class HeaderFilterCopyAction extends Extension_MailFilterAction {
	const EXTENSION_ID = 'wgm.header_filters.action.copy';
 
	function __construct($manifest) {
		$this->DevblocksExtension($manifest,1);
	}
 
	function run(Model_PreParseRule $filter, CerberusParserMessage $message) {
		$message_headers = $message->headers;
		$ticket_fields = DAO_CustomField::getAll();
		$params = $filter->actions[self::EXTENSION_ID];
		$headers = $params['headers'];
		$custom_fields = $params['custom_fields'];
 
		foreach($headers as $idx => $header) {
			if($message_headers[strtolower($header)] != null) {
				$header_value = $message_headers[strtolower($header)];
 
				// handle array headers
				if(is_array($header_value)) {
					$value = DevblocksPlatform::parseCrlfString(implode('\r\n',$header_value));
				} else {
					$value = DevblocksPlatform::parseCrlfString($header_value);
				}
 
				// collapse multi-line headers to single line for single-line text fields
				if($ticket_fields[$custom_fields[$idx]]->type == Model_CustomField::TYPE_SINGLE_LINE) {
					$message->custom_fields[$custom_fields[$idx]] 
						= trim(implode(' ',$value));
				} elseif($ticket_fields[$custom_fields[$idx]]->type == Model_CustomField::TYPE_MULTI_LINE) {
					$message->custom_fields[$custom_fields[$idx]] 
						= trim(implode('\r\n',$value));
				}
			}
		}
	}
 
	function renderConfig(Model_PreParseRule $filter=null) {
		$tpl = DevblocksPlatform::getTemplateService();
		$path = dirname(__FILE__) . '/templates/';
 
		$groups = DAO_Group::getAll();
		$tpl->assign('groups', $groups);
 
		$ticket_fields = DAO_CustomField::getBySource('cerberusweb.fields.source.ticket');
        $tpl->assign('ticket_fields', $ticket_fields);
		$tpl->assign('ticket_fields', $ticket_fields);
 
		$params = $filter->actions[self::EXTENSION_ID];
		$tpl->assign('headers',$params['headers']);
		$tpl->assign('custom_fields',$params['custom_fields']);
 
		$tpl->display($path.'header_filter_action.tpl');
	}
 
	function saveConfig() {
		$headers = DevblocksPlatform::importGPC($_REQUEST['header'],'array',array());
		$custom_fields = DevblocksPlatform::importGPC($_REQUEST['custom_field'],'array',array());
 
		return array(
			'headers' => $headers,
			'custom_fields' => $custom_fields,
		);
	}
 
};
