@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Asignar Rol al Usuario: {{ $user->name }}</h3>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('users.updateRole', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <select name="role">
            @foreach($roles as $role)
                <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">Actualizar Rol</button>
    </form>
</div>
@endsection
