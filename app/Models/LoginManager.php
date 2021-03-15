<?php

declare(strict_types=1);

namespace App\Models;

use Nette\SmartObject;
use App\Models\DatabaseModel;
use Nette\Database\Connection;

class LoginManager extends DatabaseModel
{
    use SmartObject;

    /**
     * Constants for work with DB.
     *
     */
    const
    TABLE_NAME = 'users',
    COLUMN_ID = 'id',
    FIRSTNAME = 'firstname',
    LASTNAME = 'lastname',
    EMAIL = 'email',
    PASSWORD = 'password';

    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $password = '';
    public int $userId = 0;

    private $database;

    /**
     * add Nette\Database\Connection
     * i may not need as the DatabaseModel has got the construst...
     * remove after testing it!?
    */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function getUser($name, $password)
    {
        $row = $this->database->fetchAll('SELECT * FROM users WHERE firstname = ? AND passwords = ?', trim($name), trim($password));

        if (empty($row)) {
            return "not found";
        } else {
            $this->userId = ($row[0]['id']);
            $this->firstName = ($row[0]['firstname']);
            $this->lastName = ($row[0]['lastname']);
            $this->password = ($row[0]['passwords']);

            return ['user' => $this->firstName, 'user_id' => $this->userId];
        }
    }

    public function registerUser($f_name, $l_name, $email, $password, $repassword)
    {
        //sanatise & trim inputs?
        trim($email);
        trim($f_name);
        trim($l_name);

        //check is the email exist -> flash msg if yes
        $row = $this->database->fetchAll('SELECT * FROM users WHERE email = ?', $email);

        if (empty($row)) {
            //check if the confirm password match
            if ($password === $repassword) {
                $this->database->query('INSERT INTO users ?', [
                    'email' => $email,
                    'firstname' => $f_name,
                    'lastname' => $l_name,
                    'passwords' => $password
                ]);

                // return auto-increment of the inserted row
                $id = $this->database->getInsertId();
                if ($id) {
                    return "registered";
                } else {
                    return "register error";
                }
            } else {
                return "repassword";
            }
        } else {
            return "email";
        }
    }
}
