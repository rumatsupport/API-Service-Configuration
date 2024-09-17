<?php

namespace App\Rules;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class FormRequest
{
    use ProvidesConvenienceMethods;

    public Request $req;

    public function __construct(Request $request, array $messages = [], array $customAttributes = [])
    {
        $this->req = $request;

        $this->token = \str_replace("Bearer ","",$this->req->header('Authorization'));

        $messages = $this->messages();

        $this->validate($this->req, $this->rules($this->token), $messages, $customAttributes);
    }

    public function all()
    {
        return $this->req->all();
    }

    public function get(string $key, $default = null)
    {
        return $this->req->get($key, $default);
    }

    public function input(string $key = null, $default = null)
    {
        return $this->req->input($key, $default);
    }

    public function except(array $key)
    {
        return $this->req->except($key);
    }

    public function only(array $key)
    {
        return $this->req->only($key);
    }

    public function add(array $input)
    {
        return $this->req->request->add($input);
    }

    public function merge(array $input)
    {
        return $this->req->merge($input);
    }

    public function remove(string $key)
    {
        return $this->req->request->remove($key);
    }

    public function keyName(array $request)
    {
        return array_keys($request);
    }

    protected function rules(string $token)
    {
        return [];
    }

    protected function messages()
    {
        return [];
    }
}
