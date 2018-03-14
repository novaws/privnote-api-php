privnote-api-php
===============

A simple class for making calls to prvnote's using PHP.

Getting Started
---------------
1. Include Privnote.php into your PHP script:

    ```php
    require_once('Privnote.php');
    ```
2. Initialize object:

    ```php
    $obj = new Privnote();
    ```
2. Send test data:

    ```php
    echo $obj->note('TEST');
    ```