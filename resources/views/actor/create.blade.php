
<form id="createActorForm">
	<fieldset>
		<legend>New actor</legend>
		<table class="table table-sm table-hover">
                <tr><td><label for="name" title="{{ $tableComments['name'] }}">Name</label> 
                </td><td><input id="name" class="form-control form-control-sm " name="name" >
                </td><td><label for="first_name" title="{{ $tableComments['first_name'] }}">First name</label>
                </td><td><input id="first_name" class="form-control form-control-sm " name="first_name" >
                </tr><tr><td><label for="display_name" title="{{ $tableComments['display_name'] }}">Display name</label>
                </td><td><input type='text' class=" form-control form-control-sm" name="display_name">
                </td><td><label for="login" title="{{ $tableComments['login'] }}">Login</label>
                </td><td><input id="login" class="form-control form-control-sm" name="login" >
                </tr><tr><td><label for="drole_new" title="{{ $tableComments['default_role'] }}">Default role</label>
                </td><td class="ui-front">
                		<input class="form-control form-control-sm" name="drole_new">
                		<input type='hidden' name='default_role' id='default_role' >
                </td><td><label for="function" title="{{ $tableComments['function'] }}">Function</label>
                </td><td><input class="  form-control form-control-sm" name="function" id='function' >
				</tr><tr><td><label for="company_new" title="{{ $tableComments['company_id'] }}">Employer</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control form-control-sm" name="company_new">
                		<input type='hidden' name='company_id' id='company_id' >
                </td><td><label for="parent_new" title="{{ $tableComments['parent_id'] }}">Parent company</label>
                </td><td class="ui-front">
					<input type="text" class="form-control form-control-sm" name="parent_new" >
					<input type='hidden' name='parent_id' id='parent_id' >
                </tr><tr><td><label for="site_new" title="{{ $tableComments['site_id'] }}">Work site</label>
                </td><td class="ui-front">
                		<input type="text" class="form-control form-control-sm" name="site_new">
                		<input type='hidden' name='site_id' id='site_id' >
                </td><td><label for="phy_person" title="{{ $tableComments['phy_person'] }}">Person</label>
                </td><td><span class="form-control form-control-sm" name="phy_person">
                        <input type="radio" name="phy_person" value="1"/> Physical&nbsp;&nbsp;
                        <input type="radio" name="phy_person" value="0"/> Legal
                     </span>
				</tr><tr><td><label for="nationality_new" title="{{ $tableComments['nationality'] }}">Nationality</label>
                </td><td class="ui-front">
                		<input class="form-control form-control-sm" name="nationality_new">
                		<input type='hidden' name='nationality' id='nationality' >
                </td><td><label for="small_entity" title="{{ $tableComments['small_entity'] }}">Small entity</label>
                </td><td><span class="form-control form-control-sm" name="small_entity">
                        <input type="radio" name="small_entity" value="1"/>YES&nbsp;&nbsp;
                        <input type="radio" name="small_entity" value="0"/>NO
                     </span>
				</td></tr>
				</table>
        </fieldset>
        <fieldset>
                <legend>Contact details</legend>
                <table class="table table-sm table-hover">
                <tr><td><label for="address" title="{{ $tableComments['address'] }}">Address</label>
                </td><td><textarea id="address" class=" form-control form-control-sm" name="address" ></textarea>
                </td><td><label for="country_new" title="{{ $tableComments['country'] }}">Country</label>
                </td><td class="ui-front">
						<input type="text" class="form-control form-control-sm" name="country_new">
						<input type='hidden' name='country' id='country' >
                </td><td>
                </td></tr><tr><td><label for="address_mailing" title="{{ $tableComments['address_mailing'] }}">Address mailing</label>
                </td><td><textarea class=" form-control form-control-sm" id="address_mailing" name="address_mailing"></textarea>
                </td><td><label for="country_mailing_new" title="{{ $tableComments['country_mailing'] }}">Counntry mailing</label> 
                </td><td class="ui-front">
						<input type="text" class="form-control form-control-sm" name="country_mailing_new">
						<input type='hidden' name='country_mailing' id='country_mailing' >
                </td></tr><tr><td><label for="address_billing" title="{{ $tableComments['address_billing'] }}">Address billing</label>
                </td><td><textarea class=" form-control form-control-sm" name="address_billing" id="address_billing"></textarea>
                </td><td><label type='text' for="country_billing_new" title="{{ $tableComments['country_billing'] }}">Country billing</label> 
                </td><td class="ui-front">
						<input class="form-control form-control-sm" name="country_billing_new">
						<input type='hidden' name='country_billing' id='country_billing' >
                </td></tr><tr><td><label for="email" title="{{ $tableComments['email'] }}">Email</label>
                </td><td><input class=" form-control form-control-sm" name="email" id='email' >
				</td><td><label for="phone" title="{{ $tableComments['phone'] }}">Phone</label>
                </td><td><input class="  form-control form-control-sm" name="phone" id='phone' >
				</td></tr>
			</table>
        </fieldset>
        <fieldset>
                <legend>Other details</legend>
			<table class="table table-sm table-hover">
                <tr></td><td><label for="VAT_number" title="{{ $tableComments['VAT_number'] }}">Vat no.</label>
                </td><td><input id="VAT_number" class=" form-control form-control-sm" name="VAT_number" >
                <td><label for="warn" title="{{ $tableComments['warn'] }}">Warn</label>
                </td><td><span class=" " name="warn">
                        <input type="radio" name="warn" value="1"/> YES&nbsp;&nbsp;
                        <input type="radio" name="warn" value="0"/> NO
                     </span>
                </td></tr><tr><td><label for="registration_no" title="{{ $tableComments['registration_no'] }}">Registration no.</label>
                </td><td><input type='text' class=" form-control form-control-sm" name="registration_no">
                </td><td><label for="legal_form" title="{{ $tableComments['legal_form'] }}">Legal form</label> 
                </td><td><input type='text' class="  form-control form-control-sm" name="legal_form">
                </td></tr><tr><td><label  for="notes" title="{{ $tableComments['notes'] }}">Notes</label>
                </td><td colspan="3"><textarea class=" form-control form-control-sm" name="notes"></textarea></td>
                </tr>
			</table>
		</fieldset>
    <div id="error-box">
    </div>
	<button type='submit'>Create actor</button>
</form>

