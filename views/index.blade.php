@extends('shopifyauth::layouts.auth')

@section('styles')
    <style>

        form {
            font-size: 1em;
        }
        form input {
            padding: 8px 12px;
            border: none;
            display: inline-block;
            border: #eee 1px solid;
            border-radius: 3px;
        }
        form span {
            display: inline-block;
        }
        form button {
            margin-left: 12px;
            padding: 8px 12px;
            display: inline-block;
        }

    </style>
@endsection

@section('contents')
    <form>
        <input type="text" name="shop" placeholder="Enter your shop name" /><span>.myshopify.com</span><button>Submit</button>
    </form>
@endsection