Specify the header to copy to the selected Custom Field:
<br>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	{section name=header_section start=0 loop=5 max=5 step=1}
		<tr>
			<td width="50%" valign="top">
				<input type="text" name="header[]" size="45" value="{$headers[$smarty.section.header_section.index]}"></input>
			</td>
			<td width="50%" valign="top">
				<select name="custom_field[]">
					<option value=""></option>
					{foreach from=$ticket_fields item=f key=f_id}
						{if $f->type == "S" || $f->type == "T"}
							{assign var=field_group_id value=$f->group_id}
							<option value="{$f_id}" {if $f_id==$custom_fields[$smarty.section.header_section.index]}selected{/if}>
								{if isset($groups.$field_group_id)}{$groups.$field_group_id->name}: {/if}{$f->name|escape}
							</option>
						{/if}
					{/foreach}
				</select>
			</td>
		</tr>
	{/section}
</table>
