    
<style>
.actor-input-wide {
	display: inline-block;
	width: 230px;
	border: 1px solid #FFF;
	background: #FFF;
	padding: 1px 2px;
	vertical-align: top;
	margin-bottom: 3px;
	min-height: 16px;
}

.actor-input-narrow {
	display: inline-block;
	width: 125px;
	border: 1px solid #FFF;
	background: #FFF;
	padding: 1px 2px;
	vertical-align: top;
	margin-bottom: 3px;
	min-height: 16px;
}

.teditable {
	min-height: 32px;
}

.close-button {
	background: #f00;
	float: right;
	padding: 2px 4px 0px;
	cursor: pointer;
	font-family: arial;
}

.validation-errors {
	color: #F00;
	padding: 5px;
}

#valid-error {
	display: block;
	margin: 0px 0px 5px 10px;
}

.actor-info-set {
	background: #EFEFEF;
	border: 1px inset #888;
}

input {
	border: 0px;
}
</style>

<div id="edit-actor-content">
	<fieldset class="actor-info-set">
		<legend>Actor details - ID: {{ $actorInfo->id }}</legend>
		<table data-id="{{ $actorInfo->id }}">
                <tr><td><label for="name" class="required-field" title="{{ $actorComments['name'] }}">Name</label> 
                </td><td class="ui-front"><input class="actor-input-wide noformat" name="name" value="{{ $actorInfo->name }}">
                </td><td><label for="first_name" title="{{ $actorComments['first_name'] }}">First name</label>
                </td><td><input id="first_name" class="actor-input-narrow noformat" name="first_name" value="{{ $actorInfo->first_name }}">
                </tr><tr><td><label for="display_name" title="{{ $actorComments['display_name'] }}">Display name</label>
                </td><td class="ui-front">
                		<input type="text" class="actor-input-wide noformat" name="display_name" value="{{ $actorInfo->display_name }}">
                </td><td><label for="login" title="{{ $actorComments['login'] }}">Login</label>
                </td><td><input id="login" class="actor-input-narrow noformat" name="login" value="{{ $actorInfo->login }}">
                </tr><tr><td><label for="default_role" title="{{ $actorComments['default_role'] }}">Default role</label>
                </td><td class="ui-front">
                		<input type="text" class="actor-input-wide" name="default_role" value="{{ $actorInfo->drole_name }}">
                </td><td><label for="function" title="{{ $actorComments['function'] }}">Function</label>
                </td><td><input id="function" class="actor-input-narrow noformat" name="function" value="{{ $actorInfo->function }}">
                </tr><tr><td><label for="company_id" title="{{ $actorComments['company_id'] }}">Employer</label>
                </td><td class="ui-front">
                		<input type="text" class="actor-input-wide" id="company_id" name="company_id" value="{{ $actorInfo->company_name }}">
                <td><label for="parent_id" title="{{ $actorComments['parent_id'] }}">Parent company</label>
                </td><td class="ui-front">
                		<input type="text" class="actor-input-wide" id="parent_id" name="parent_id" value="{{ $actorInfo->parent_name }}">
                </tr><tr><td><label for="site_id" title="{{ $actorComments['site_id'] }}">Work site</label>
                </td><td class="ui-front">
                		<input type="text" class="actor-input-wide" id="site_id" name="site_id" value="{{ $actorInfo->site_name }}">
                </td><td><label for="phy_person" title="{{ $actorComments['phy_person'] }}">Person</label>
                </td><td><span class="actor-input-narrow" name="phy_person">
                        <input type="radio" name="phy_person" id="phy_person" value="1" {{ $actorInfo->phy_person ? 'checked="checked"' : "" }} />Physical&nbsp;&nbsp;
                        <input type="radio" name="phy_person" id="phy_person" value="0" {{ $actorInfo->phy_person ? "" : 'checked="checked"'}} />Legal
                </span>
                </tr><tr><td><label for="nationality" title="{{ $actorComments['nationality'] }}">Nationality</label>
                </td><td class="ui-front">
                		<input type="text" class="actor-input-wide" name="nationality" value="{{ $actorInfo->nationality_name }}">
                </td><td><label for="small_entity" title="{{ $actorComments['small_entity'] }}">Small entity</label>
                </td><td><span class="actor-input-narrow" name="small_entity">
                        <input type="radio" name="small_entity" id="small_entity" value="1" {{ $actorInfo->small_entity ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="small_entity" id="small_entity" value="0" {{ $actorInfo->small_entity ? "" : 'checked="checked"'}} />No
                </span>
				</td></tr>
			</table>
        </fieldset>
        <fieldset class="actor-info-set">
              <legend>Contact details</legend>
              <table data-id="{{ $actorInfo->id }}">
                <tr><td><label for="address" title="{{ $actorComments['address'] }}">Address</label>
                </td><td class="ui-front">
					<input type='text' class="actor-input-wide noformat" name="address" value="{{ $actorInfo->address }}">
                </td><td><label for="country" title="{{ $actorComments['country'] }}">Country</label>
                </td><td class="ui-front">
						<input type='text' class="actor-input-narrow" name="country" value="{{ $actorInfo->country_name }}">
				</tr><tr><td><label for="address_mailing" title="{{ $actorComments['address_mailing'] }}">Address mailing</label>
                </td><td class="ui-front">
					<input type='text' class="actor-input-wide noformat" name="address_mailing" value="{{ $actorInfo->address_mailing }}">
                </td><td><label for="country_mailing" title="{{ $actorComments['country_mailing'] }}">Country mailing</label>
                </td><td class="ui-front">
						<input type='text' class="actor-input-narrow" name="country_mailing" value="{{ $actorInfo->country_mailing_name }}">
				</tr><tr><td><label for="address_billing" title="{{ $actorComments['address_billing'] }}">Address billing</label>
                </td><td class="ui-front">
					<input type='text' class="actor-input-wide noformat" name="address_billing" value="{{ $actorInfo->address_billing }}">
                </td><td><label for="country_billing" title="{{ $actorComments['country_billing'] }}">Country billing</label>
                </td><td class="ui-front">
						<input type='text' class="actor-input-narrow" name="country_billing" value="{{ $actorInfo->country_billing_name }}">
				</tr><tr><td><label for="email" title="{{ $actorComments['email'] }}">Email</label>
                </td><td class="ui-front">
					<input type='text' class="actor-input-wide noformat" name="email" value="{{ $actorInfo->email }}">
                </td><td><label for="phone" title="{{ $actorComments['phone'] }}">Phone</label>
                </td><td class="ui-front">
						<input type='text' class="actor-input-narrow noformat" name="phone" value="{{ $actorInfo->phone }}">
				</td></tr>
			</table>
        </fieldset>
        <fieldset class="actor-info-set">
              <legend>Contact details</legend>
              <table data-id="{{ $actorInfo->id }}">
				<tr><td><label for="warn" title="{{ $actorComments['warn'] }}">Warn</label>
                </td><td><span class="actor-input-narrow" name="warn">
                        <input type="radio" name="warn" value="1" {{ $actorInfo->warn ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="warn" value="0" {{ $actorInfo->warn ? "" : 'checked=checked' }}/>NO
                </span>
                </td><td><label for="VAT_number" title="{{ $actorComments['VAT_number'] }}">VAT no.</label>
                </td><td class="ui-front">
						<input type='text' class="actor-input-narrow  noformat" name="VAT_number" value="{{ $actorInfo->VAT_number }}">
                </td></tr>
				</tr><tr><td><label for="notes" title="{{ $actorComments['notes'] }}">Notes</label>
                </td><td class="ui-front">
					<input type='text' class="actor-input-wide" name="notes" value="{{ $actorInfo->notes }}">
                </td></tr>
        </table>
		<button title="Delete actor" class="delete-actor" id="{{ $actorInfo->id }}" style="float: right; margin-top: 10px; margin-right: 16px;">
			<span class="ui-icon ui-icon-trash" style="float: left;"></span>
			Delete
		</button>
	</fieldset>

	<input type="hidden" value="" id="country-code" name="country-code" /> 
	
</div>

