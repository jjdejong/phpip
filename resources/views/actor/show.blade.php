
<div id="edit-actor-content">
    <fieldset>
        <table class="table table-hover table-sm" data-id="{{ $actorInfo->id }}">
            <tr>
                <td><label for="name" class="required-field" title="{{ $actorComments['name'] }}">Name</label> </td>
                <td class="ui-front"><input class="noformat form-control" name="name" value="{{ $actorInfo->name }}"></td>
                <td><label for="first_name" title="{{ $actorComments['first_name'] }}">First name</label></td>
                <td><input id="first_name" class="noformat form-control" name="first_name" value="{{ $actorInfo->first_name }}"></td>
            </tr>
            <tr>
                <td><label for="display_name" title="{{ $actorComments['display_name'] }}">Display name</label></td>
                <td class="ui-front"><input type="text" class="noformat form-control" name="display_name" value="{{ $actorInfo->display_name }}"></td>
                <td><label for="login" title="{{ $actorComments['login'] }}">Login</label></td>
                <td><input id="login" class="noformat form-control" name="login" value="{{ $actorInfo->login }}"></td>
            </tr>
            <tr>
                <td><label for="default_role" title="{{ $actorComments['default_role'] }}">Default role</label></td>
                <td class="ui-front"><input type="text" class="noformat form-control" name="default_role" data-ac="/role/autocomplete" value="{{ empty($actorInfo->droleInfo) ? '' : $actorInfo->droleInfo->name }}"></td>
                <td><label for="function" title="{{ $actorComments['function'] }}">Function</label></td>
                <td><input id="function" class="noformat form-control" name="function" value="{{ $actorInfo->function }}"></td>
            </tr>
            <tr>
                <td><label for="company_id" title="{{ $actorComments['company_id'] }}">Employer</label></td>
                <td class="ui-front"><input type="text" class="noformat form-control" id="company_id" name="company_id" data-ac="/actor/autocomplete" value="{{ empty($actorInfo->company) ? '' : $actorInfo->company->name }}"></td>
                <td><label for="parent_id" title="{{ $actorComments['parent_id'] }}">Parent company</label></td>
                <td class="ui-front"><input type="text" class="noformat form-control" id="parent_id" name="parent_id" data-ac="/actor/autocomplete" value="{{ empty($actorInfo->parent) ? '' : $actorInfo->parent->name }}"></td>
            </tr>
            <tr>
                <td><label for="site_id" title="{{ $actorComments['site_id'] }}">Work site</label></td>
                <td class="ui-front"><input type="text" class="noformat form-control" id="site_id" name="site_id" data-ac="/actor/autocomplete" value="{{ empty($actorInfo->site) ? '' : $actorInfo->site->name }}"></td>
                <td><label for="phy_person" title="{{ $actorComments['phy_person'] }}">Person</label></td>
                <td>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="phy_person" id="phy_person" value="1" {{ $actorInfo->phy_person ? 'checked="checked"' : "" }}>
                        <label class="form-check-label" for="phy_person">Physical</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="phy_person" id="phy_person" value="0" {{ $actorInfo->phy_person ? "" : 'checked="checked"'}}>
                        <label class="form-check-label" for="phy_person">Legal</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="nationality">Nationality</label></td>
                <td class="ui-front"><input type="text" class="noformat form-control" name="nationality" data-ac="/country/autocomplete" value="{{ empty($actorInfo->nationalityInfo) ? '' : $actorInfo->nationalityInfo->name }}"></td>
                <td><label for="small_entity" title="{{ $actorComments['small_entity'] }}">Entity</label></td>
                <td>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="small_entity" id="small_entity" value="1" {{ $actorInfo->small_entity ? 'checked="checked"' : "" }}>
                        <label class="form-check-label" for="small_entity">Small</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="small_entity" id="small_entity" value="0" {{ $actorInfo->small_entity ? "" : 'checked="checked"'}}>
                        <label class="form-check-label" for="small_entity">Large</label>
                    </div>
                </td>
            </tr>

        </table>
    </fieldset>
    <fieldset>
        <legend>Contact details</legend>
        <table class="table table-hover table-sm" data-id="{{ $actorInfo->id }}">
            <tr>
                <td>
                    <label for="address">Address</label><br />
                </td>
                <td class="ui-front"><textarea class="noformat form-control" name="address">{{ $actorInfo->address }}</textarea></td>
                <td><label for="country">Country</label></td>
                <td class="ui-front"><input type='text' class="noformat form-control" name="country" data-ac="/country/autocomplete" value="{{ empty($actorInfo->countryInfo) ? '' : $actorInfo->countryInfo->name }}"></td>
            </tr>
            <tr>
                <td>
                    <label for="address_mailing">Address mailing</label><br />
                </td>
                <td class="ui-front"><textarea class="noformat form-control" name="address_mailing">{{ $actorInfo->address_mailing }}</textarea> </td>
                <td><label for="country_mailing">Country mailing</label></td>
                <td class="ui-front"><input type='text' class="noformat form-control" name="country_mailing" data-ac="/country/autocomplete" value="{{ empty($actorInfo->country_mailingInfo ) ? '' : $actorInfo->country_mailingInfo->name }}"></td>
            </tr>
            <tr>
                <td>
                    <label for="address_billing">Address billing</label><br />
                </td>
                <td class="ui-front"><textarea class="noformat form-control" name="address_billing">{{ $actorInfo->address_billing }}</textarea></td>
                <td><label for="country_billing">Country billing</label></td>
                <td class="ui-front"><input class="noformat form-control" name="country_billing" data-ac="/country/autocomplete" value="{{ empty($actorInfo->country_billingInfo ) ? '' : $actorInfo->country_billingInfo->name }}"></td>
            </tr>
            <tr>
                <td><label for="email">Email</label></td>
                <td class="ui-front"><input type='text' class="noformat form-control" name="email" value="{{ $actorInfo->email }}"></td>
                <td><label for="phone">Phone</label></td>
                <td class="ui-front"><input type='text' class="noformat form-control" name="phone" value="{{ $actorInfo->phone }}"></td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend>Administrative details</legend>
        <table class="table table-hover table-sm" data-id="{{ $actorInfo->id }}">
            <tr>
                <td><label for="VAT_number" title="{{ $actorComments['VAT_number'] }}" >VAT no.</label></td>
                <td><input type='text' class="noformat form-control" name="VAT_number" value="{{ $actorInfo->VAT_number }}"></td>
                <td><label for="warn" title="{{ $actorComments['warn'] }}">Warn</label></td>
                <td>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="warn" id="warn" value="1" {{ $actorInfo->warn ? 'checked="checked"' : "" }}>
                        <label class="form-check-label" for="warn">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="warn" id="warn" value="0" {{ $actorInfo->warn ? "" : 'checked="checked"'}}>
                        <label class="form-check-label" for="warn">No</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label for="registration_no" title="{{ $actorComments['registration_no'] }}" >Registration no.</label></td>
                <td><input type='text' class="noformat form-control" name="registration_no" value="{{ $actorInfo->registration_no }}"></td>
                <td><label for="registration_no" title="{{ $actorComments['legal_form'] }}" >Legal form</label></td>
                <td><input type='text' class="noformat form-control" name="legal_form" value="{{ $actorInfo->legal_form }}"></td>
            </tr>
            <tr>
                <td>
                    <label for="notes" title="{{ $actorComments['notes'] }}">Notes</label><br />
                </td>
                <td colspan="3" class="ui-front"><textarea class="noformat form-control" name="notes">{{ $actorInfo->notes }}</textarea></td>
            </tr>
        </table>
        <button title="Delete actor" class="btn btn-danger delete-actor" data-dismiss="modal" data-id="{{ $actorInfo->id }}">
            Delete
        </button>
        <div class="btn-group dropup float-right" id="usedInDropdown">
          <a href="/actor/{{ $actorInfo->id }}/usedin" class="btn btn-info dropdown-toggle" data-toggle="dropdown" title="Actor dependencies">
              &boxbox; Used in
          </a>
          <div class="dropdown-menu">
            Ajax Placeholder
          </div>
        </div>
    </fieldset>
</div>
