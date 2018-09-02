<?php
namespace App\Request;

/***
 * Request methods (ENUM)
 *
 * Class RequestMethod
 */
abstract class RequestMethod
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
}