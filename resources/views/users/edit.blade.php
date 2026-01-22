@extends('layouts.admin-layout')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Edit User</h6>
                <a class="btn btn-secondary btn-sm" href="{{ route('users.index') }}"> Back</a>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('users.update', $user->id) }}" class="ajax-form">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Name:</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control"
                                placeholder="Name">
                            <span class="text-danger error-text name_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Email:</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control"
                                placeholder="Email">
                            <span class="text-danger error-text email_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Mobile:</label>
                            <input type="text" name="mobile" value="{{ $user->mobile }}" class="form-control"
                                placeholder="Mobile No">
                            <span class="text-danger error-text mobile_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">State:</label>
                            <select name="state_id" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ $user->state_id == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text state_id_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Gender:</label>
                            <select name="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <span class="text-danger error-text gender_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Date of Birth:</label>
                            <input type="date" name="dob" value="{{ $user->dob }}" class="form-control">
                            <span class="text-danger error-text dob_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Password (Leave blank to keep current):</label>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                            <span class="text-danger error-text password_error"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Confirm Password:</label>
                            <input type="password" name="confirm-password" class="form-control"
                                placeholder="Confirm Password">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Role:</label>
                            <select class="form-control" name="roles[]" multiple>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}"
                                        {{ in_array($role, $userRole) ? 'selected' : '' }}>
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl (Windows) to select multiple roles.</small>
                            <span class="text-danger error-text roles_error"></span>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label">Account Active</label>
                            </div>
                        </div>

                        <div class="col-md-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary submit-btn">Update User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
