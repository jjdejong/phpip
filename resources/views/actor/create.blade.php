    
<style>
.rule-input-wide {
	display: inline-block;
	width: 200px;
	border: 1px solid #FFF;
	background: #FFF;
	padding: 1px 2px;
	vertical-align: top;
	margin-bottom: 3px;
	min-height: 16px;
}

.rule-input-narrow {
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

.rule-info-set {
	background: #EFEFEF;
	border: 1px inset #888;
}

input {
	border: 0px;
}
</style>

<form id="createActorForm">
	<fieldset class="rule-info-set">
		<legend>New actor</legend>
		<table>
                <tr><td><label for="name" title="{{ $tableComments['name'] }}">Name</label> 
                </td><td><input id="name" class="rule-input-wide noformat" name="name" >
                </td><td><label for="first_name" title="{{ $tableComments['first_name'] }}">First name</label>
                </td><td><input id="first_name" class="rule-input-narrow noformat" name="first_name" >
                </tr><tr><td><label for="display_name" title="{{ $tableComments['display_name'] }}">Display name</label>
                </td><td><input type='text' class="rule-input-wide noformat" name="display_name">
                </td><td><label for="login" title="{{ $tableComments['login'] }}">Login</label>
                </td><td><input id="login" class="rule-input-narrow noformat" name="login" >
                </tr><tr><td><label for="drole_new" title="{{ $tableComments['default_role'] }}">Default role</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="drole_new">
                		<input type='hidden' name='default_role' id='default_role' >
                </td><td><label for="function" title="{{ $tableComments['function'] }}">Function</label>
                </td><td><input class="rule-input-narrow" name="function" id='function' >
				</tr><tr><td><label for="company_new" title="{{ $tableComments['company_id'] }}">Employer</label>
                </td><td class="ui-front">
                		<input type="text" class="rule-input-wide" name="company_new">
                		<input type='hidden' name='company_id' id='company_id' >
                </td><td><label for="parent_new" title="{{ $tableComments['parent_id'] }}">Parent company</label>
                </td><td class="ui-front">
					<input type="text" class="rule-input-narrow" name="parent_new" >
					<input type='hidden' name='parent_id' id='parent_id' >
                </tr><tr><td><label for="site_new" title="{{ $tableComments['site_id'] }}">Work site</label>
                </td><td class="ui-front">
                		<input type="text" class="rule-input-wide" name="site_new">
                		<input type='hidden' name='site_id' id='site_id' >
                </td><td><label for="phy_person" title="{{ $tableComments['phy_person'] }}">Person</label>
                </td><td><span class="rule-input-narrow " name="phy_person">
                        <input type="radio" name="phy_person" value="1"/> Physical&nbsp;&nbsp;
                        <input type="radio" name="phy_person" value="0"/> Legal
                     </span>
				</tr><tr><td><label for="nationality_new" title="{{ $tableComments['nationality'] }}">Nationality</label>
                </td><td class="ui-front">
                		<input class="rule-input-wide" name="nationality_new">
                		<input type='hidden' name='nationality' id='nationality' >
                </td><td><label for="small_entity" title="{{ $tableComments['small_entity'] }}">Small entity</label>
                </td><td><span class="rule-input-narrow " name="small_entity">
                        <input type="radio" name="small_entity" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="small_entity" value="0"/>NO
                     </span>
				</td></tr>
				</table>
        </fieldset>
        <fieldset class="rule-info-set">
                <legend>Contact details</legend>
                <table>
                <tr><td><label for="address" title="{{ $tableComments['address'] }}">Address</label>
                </td><td><input id="address" class="rule-input-wide noformat" name="address" >
                </td><td><label for="country_new" title="{{ $tableComments['country'] }}">Country</label>
                </td><td class="ui-front">
						<input type="text" class="rule-input-narrow" name="country_new">
						<input type='hidden' name='country' id='country' >
                </td><td>
                </td></tr><tr><td><label for="address_mailing" title="{{ $tableComments['address_mailing'] }}">Address mailing</label>
                </td><td><input type='text' class="rule-input-wide noformat" id="address_mailing" name="address_mailing">
                </td><td><label for="country_mailing_new" title="{{ $tableComments['country_mailing'] }}">Counntry mailing</label> 
                </td><td class="ui-front">
						<input type="text" class="rule-input-narrow" name="country_mailing_new">
						<input type='hidden' name='country_mailing' id='country_mailing' >
                </td></tr><tr><td><label for="address_billing" title="{{ $tableComments['address_billing'] }}">Address billing</label>
                </td><td><input type='text' class="rule-input-wide noformat" name="address_billing" id="address_billing">
                </td><td><label type='text' for="country_billing_new" title="{{ $tableComments['country_billing'] }}">Country billing</label> 
                </td><td class="ui-front">
						<input class="rule-input-narrow" name="country_billing_new">
						<input type='hidden' name='country_billing' id='country_billing' >
                </td></tr><tr><td><label for="email" title="{{ $tableComments['email'] }}">Email</label>
                </td><td><input class="rule-input-wide" name="email" id='email' >
				</td><td><label for="phone" title="{{ $tableComments['phone'] }}">Phone</label>
                </td><td><input class="rule-input-narrow" name="phone" id='phone' >
				</td></tr>
			</table>
        </fieldset>
        <fieldset class="rule-info-set">
                <legend>Other details</legend>
			<table>
                <tr></td><td><label for="VAT_number" title="{{ $tableComments['VAT_number'] }}">Vat no.</label>
                </td><td><input id="VAT_number" class="rule-input-wide noformat" name="VAT_number" >
                <td><label for="warn" title="{{ $tableComments['warn'] }}">Warn</label>
                </td><td><span class="rule-input-narrow " name="warn">
                        <input type="radio" name="warn" value="1"/> YES&nbsp;&nbsp;
                        <input type="radio" name="warn" value="0"/> NO
                     </span>
                </td></tr><tr><td><label for="registration_no" title="{{ $tableComments['registration_no'] }}">Registration no.</label>
                </td><td><input type='text' class="rule-input-wide noformat" name="registration_no">
                </td><td><label for="legal_form" title="{{ $tableComments['legal_form'] }}">Legal form</label> 
                </td><td><input type='text' class="rule-input-narrow noformat" name="legal_form">
                </td></tr><tr><td><label  for="notes" title="{{ $tableComments['notes'] }}">Notes</label>
                </td><td><input type='text' class="rule-input-narrow noformat" name="notes">
                </td></tr>
			</table>
		</fieldset>
    <div id="error-box">
    </div>
	<button type='submit'>Create actor</button>
</form>

