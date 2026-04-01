<div class="form-group">
    <label>Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', optional($userModel)->name) }}" required>
</div>
<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', optional($userModel)->email) }}" required>
</div>
<div class="form-group" style="max-width:220px;">
    <label>Role</label>
    <select name="role" class="form-control">
        @foreach(\App\User::roleOptions() as $value => $label)
            <option value="{{ $value }}" {{ old('role', optional($userModel)->role ?? \App\User::ROLE_AUTHOR) === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="d-flex gap-2" style="flex-wrap:wrap;">
    <div class="form-group" style="flex:1;min-width:240px;">
        <label>Password {{ $userModel ? '(Kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ $userModel ? '' : 'required' }}>
    </div>
    <div class="form-group" style="flex:1;min-width:240px;">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" {{ $userModel ? '' : 'required' }}>
    </div>
</div>
