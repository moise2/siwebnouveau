<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $roles = Role::whereNotIn('name', ['admin', 'user'])->get();
       
        
        return view('frontend.auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        //var_dump($request->all());
        //echo intval($request->institution_id);
       //exit();
       

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'institution_id' => 'required',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $validatedData['role_id'],
            'institution_id' =>intval($request->institution_id),
            'is_active' => false // Compte inactif par défaut
        ]);

       

        return view('frontend.auth.after_register');
    }
} 