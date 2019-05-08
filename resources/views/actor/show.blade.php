
<div id="edit-actor-content">
	<fieldset>
		<legend>Actor details - ID: {{ $actorInfo->id }}</legend>
		<table class="table table-hover table-sm" data-id="{{ $actorInfo->id }}">
                <tr><td><label for="name" class="required-field" title="{{ $actorComments['name'] }}">Name</label> 
                </td><td class="ui-front"><input class="noformat form-control form-control-sm" name="name" value="{{ $actorInfo->name }}">
                </td><td><label for="first_name" title="{{ $actorComments['first_name'] }}">First name</label>
                </td><td><input id="first_name" class="noformat form-control form-control-sm" name="first_name" value="{{ $actorInfo->first_name }}">
                </tr><tr><td><label for="display_name" title="{{ $actorComments['display_name'] }}">Display name</label>
                </td><td class="ui-front">
                		<input type="text" class="noformat form-control form-control-sm" name="display_name" value="{{ $actorInfo->display_name }}">
                </td><td><label for="login" title="{{ $actorComments['login'] }}">Login</label>
                </td><td><input id="login" class="noformat form-control form-control-sm" name="login" value="{{ $actorInfo->login }}">
                </tr><tr><td><label for="default_role" title="{{ $actorComments['default_role'] }}">Default role</label>
                </td><td class="ui-front">
                		<input type="text" class="noformat form-control form-control-sm" name="default_role" value="{{ empty($actorInfo->droleInfo) ? '' : $actorInfo->droleInfo->name }}">
                </td><td><label for="function" title="{{ $actorComments['function'] }}">Function</label>
                </td><td><input id="function" class="noformat form-control form-control-sm" name="function" value="{{ $actorInfo->function }}">
                </tr><tr><td><label for="company_id" title="{{ $actorComments['company_id'] }}">Employer</label>
                </td><td class="ui-front">
                		<input type="text" class="noformat form-control form-control-sm" id="company_id" name="company_id" value="{{ empty($actorInfo->company) ? '' : $actorInfo->company->name }}">
                <td><label for="parent_id" title="{{ $actorComments['parent_id'] }}">Parent company</label>
                </td><td class="ui-front">
                		<input type="text" class="noformat form-control form-control-sm" id="parent_id" name="parent_id" value="{{ empty($actorInfo->parent) ? '' : $actorInfo->parent->name }}">
                </tr><tr><td><label for="site_id" title="{{ $actorComments['site_id'] }}">Work site</label>
                </td><td class="ui-front">
                		<input type="text" class="noformat form-control form-control-sm" id="site_id" name="site_id" value="{{ empty($actorInfo->site) ? '' : $actorInfo->site->name }}">
                </td><td><label for="phy_person" title="{{ $actorComments['phy_person'] }}">Person</label>
                </td><td><span class="form-control form-control-sm" name="phy_person">
                        <input type="radio" name="phy_person" id="phy_person" value="1" {{ $actorInfo->phy_person ? 'checked="checked"' : "" }} />Physical&nbsp;&nbsp;
                        <input type="radio" name="phy_person" id="phy_person" value="0" {{ $actorInfo->phy_person ? "" : 'checked="checked"'}} />Legal
                </span>
                </tr><tr><td><label for="nationality" title="{{ $actorComments['nationality'] }}">Nationality</label>
                </td><td class="ui-front">
                		<input type="text" class="noformat form-control form-control-sm" name="nationality" value="{{ empty($actorInfo->nationalityInfo) ? '' : $actorInfo->nationalityInfo->name }}">
                </td><td><label for="small_entity" title="{{ $actorComments['small_entity'] }}">Small entity</label>
                </td><td><span class=" form-control form-control-sm" name="small_entity">
                        <input type="radio" name="small_entity" id="small_entity" value="1" {{ $actorInfo->small_entity ? 'checked="checked"' : "" }} />Yes&nbsp;&nbsp;
                        <input type="radio" name="small_entity" id="small_entity" value="0" {{ $actorInfo->small_entity ? "" : 'checked="checked"'}} />No
                </span>
				</td></tr>
			</table>
        </fieldset>
        <fieldset>
              <legend>Contact details</legend>
              <table class="table table-hover table-sm" data-id="{{ $actorInfo->id }}">
                <tr><td><label for="address" title="{{ $actorComments['address'] }}">Address</label><br />
                    <button type="button" data-field="address" class="area hidden-action btn btn-primary btn-sm" id="updateAddress">&#9432; Save</button>
               </td><td class="ui-front">
                    <textarea id="address" data-field="#updateAddress" class="noformat form-control form-control-sm" name="address">{{ $actorInfo->address }}</textarea>
                </td><td><label for="country" title="{{ $actorComments['country'] }}">Country</label>
                </td><td class="ui-front">
						<input type='text' class="noformat form-control form-control-sm" name="country" value="{{ empty($actorInfo->countryInfo) ? '' : $actorInfo->countryInfo->name }}">
				</tr><tr><td><label for="address_mailing" title="{{ $actorComments['address_mailing'] }}">Address mailing</label><br />
                    <button type="button" data-field="address_mailing" class="area hidden-action btn btn-primary btn-sm" id="updateAddressM">&#9432; Save</button>
                </td><td class="ui-front">
					<textarea data-field="#updateAddressM" id="address_mailing" class="noformat form-control form-control-sm" name="address_mailing">{{ $actorInfo->address_mailing }}</textarea> 
                </td><td><label for="country_mailing" title="{{ $actorComments['country_mailing'] }}">Country mailing</label>
                </td><td class="ui-front">
						<input type='text' class="noformat form-control form-control-sm" name="country_mailing" value="{{ empty($actorInfo->country_mailingInfo ) ? '' : $actorInfo->country_mailingInfo->name }}">
				</tr><tr><td><label for="address_billing" title="{{ $actorComments['address_billing'] }}">Address billing</label><br />
                    <button type="button" data-field="address_billing" class="area hidden-action btn btn-primary btn-sm" id="updateAddressB">&#9432; Save</button>
                </td><td class="ui-front">
					<textarea data-field="#updateAddressB" id="address_billing" class="noformat form-control form-control-sm" name="address_billing">{{ $actorInfo->address_billing }}</textarea>
                </td><td><label for="country_billing" title="{{ $actorComments['country_billing'] }}">Country billing</label><br />
                    <button type="button" class="hidden-action btn btn-warning btn-sm" id="updateAddressB">&#9432; Save</button>
                </td><td class="ui-front">
						<input class="noformat form-control form-control-sm" name="country_billing" value="{{ empty($actorInfo->country_billingInfo ) ? '' : $actorInfo->country_billingInfo->name }}">
				</tr><tr><td><label for="email" title="{{ $actorComments['email'] }}">Email</label>
                </td><td class="ui-front">
					<input type='text' class="noformat form-control form-control-sm" name="email" value="{{ $actorInfo->email }}">
                </td><td><label for="phone" title="{{ $actorComments['phone'] }}">Phone</label>
                </td><td class="ui-front">
						<input type='text' class="noformat form-control form-control-sm" name="phone" value="{{ $actorInfo->phone }}">
				</td></tr>
			</table>
        </fieldset>
        <fieldset>
              <legend>Administrative details</legend>
              <table class="table table-hover table-sm" data-id="{{ $actorInfo->id }}">
				<tr><td><label for="VAT_number" title="{{ $actorComments['VAT_number'] }}" >VAT no.</label>
                </td><td><input type='text' class="noformat form-control form-control-sm" name="VAT_number" value="{{ $actorInfo->VAT_number }}">
                </td><td><label for="warn" title="{{ $actorComments['warn'] }}">Warn</label>
                </td><td><span class=" form-control form-control-sm" name="warn">
                        <input type="radio" name="warn" value="1" {{ $actorInfo->warn ? 'checked=checked' : "" }}/>YES&nbsp;&nbsp;
                        <input type="radio" name="warn" value="0" {{ $actorInfo->warn ? "" : 'checked=checked' }}/>NO
                </span>
                </td></tr><tr><td><label for="registration_no" title="{{ $actorComments['registration_no'] }}" >Registration no.</label>
                </td><td><input type='text' class="noformat form-control form-control-sm" name="registration_no" value="{{ $actorInfo->registration_no }}">
                </td><td><label for="registration_no" title="{{ $actorComments['legal_form'] }}" >Legal form</label>
                </td><td><input type='text' class="noformat form-control form-control-sm" name="legal_form" value="{{ $actorInfo->legal_form }}">
                </td></tr><tr><td><label for="notes" title="{{ $actorComments['notes'] }}">Notes</label><br />
                    <button type="button" data-field="notes" id="updateNotes" class="area hidden-action btn btn-primary btn-sm">&#9432; Save</button>
                </td><td class="ui-front">
                    <textarea data-field="#updateNotes" id="notes" class="noformat form-control form-control-sm" name="address_billing">{{ $actorInfo->notes }}</textarea>
                </td></tr>
        </table>
        
        <a href="/actor/{{ $actorInfo->id }}/usedin" data-toggle="modal" data-target="#usedModal" data-remote="false" title="Actor used in matters or other actors" data-resource="/actor/">
            &boxbox;
            Used in
        </a>
        <button title="Delete actor" class="delete-actor" data-dismiss="modal" data-id="{{ $actorInfo->id }}" style="float: right; margin-top: 10px; margin-right: 16px;">
            &times;
            Delete
        </button>
    </fieldset>
	
</div>
