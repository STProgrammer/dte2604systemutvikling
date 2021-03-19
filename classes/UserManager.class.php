<?php


class UserManager
{
    private $dbase;
    private $request;
    private $session;

    public function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->dbase = $db;
        $this->request = $request;
        $this->session = $session;
    }



    private function notifyUser($strHeader, $strMessage = "")
    {
        $this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    // REGISTER USER
    public function registerUser (User $user) : bool
    {
        $username = $this->request->request->get('username');
        $firstName = $this->request->request->get('firstName');
        $lastName = $this->request->request->get('lastName');
        $emailAddress = $this->request->request->get('emailAddress');
        $password = $this->request->request->get('password');
        $address = $this->request->request->get('address');
        $city = $this->request->request->get('city');
        $zipCode = $this->request->request->get('zipCode');
        $phoneNumber = $this->request->request->get('phoneNumber');
        $mobileNumber = $this->request->request->get('mobileNumber');
        $IMAddress = $this->request->request->get('IMAddress');
        $userType = $this->request->request->get('userType', 1);
        $isCustomer = $userType == 3 ? 1: 0;
        $status = $isCustomer ? "N/A": "Free";
        $isTemporary = $userType == 2 ? 1: 0;
        $isAdmin = ($userType == 4 && $user->isAdmin()) ? 1: 0;
        $isVerifiedByAdmin = $user->isAdmin() ? 1:0;
        try{
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("insert into Users (username, firstName, lastName, emailAddress, address, city, zipCode, phoneNumber, mobileNumber, IMAddress, password, dateRegistered, isCustomer, isTemporary, isVerifiedByAdmin, 
                   isEmailVerified, isAdmin, status) values (:username, :firstName, :lastName, :emailAddress, :address, :city, :zipCode, :phoneNumber, :mobileNumber, :IMAddress, :password, NOW(), :isCustomer, :isTemporary, :isVerifiedByAdmin, 1, :isAdmin, :status);");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
            $sth->bindParam(':lastName',  $lastName, PDO::PARAM_STR);
            $sth->bindParam(':emailAddress', $emailAddress, PDO::PARAM_STR);
            $sth->bindParam(':address', $address, PDO::PARAM_STR);
            $sth->bindParam(':city', $city, PDO::PARAM_STR);
            $sth->bindParam(':zipCode',  $zipCode, PDO::PARAM_STR);
            $sth->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            $sth->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
            $sth->bindParam(':IMAddress', $IMAddress, PDO::PARAM_STR);
            $sth->bindParam(':password', $hash, PDO::PARAM_STR);
            $sth->bindParam(':isCustomer',  $isCustomer, PDO::PARAM_INT);
            $sth->bindParam(':isTemporary', $isTemporary, PDO::PARAM_INT);
            $sth->bindParam(':isVerifiedByAdmin', $isVerifiedByAdmin, PDO::PARAM_INT);
            $sth->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Ny bruker ble registrert", "");
                return true;
            } else {
                $this->notifyUser("Failed to register user!", "");
                return false;
            }
            /*if ($this->sendEmail($email)) { $this->notifyUser("User registered", "");}
            else {$this->notifyUser("Failed to send email to verify!", ""); }*/
        } catch (Exception $e) {
            $this->notifyUser("Failed to register user!", $e->getMessage());
            return false;
        }
    }
    // END REGISTER USER


    // VERIFY USER BY ADMIN
    public function verifyUserByAdmin($userID) : bool {
        if($this->session->get('User')->isAdmin()) {
            try {
                $sth = $this->dbase->prepare("update Users set isVerifiedByAdmin = 1 where UserID = :UserID");
                $sth->bindParam(':UserID', $userID, PDO::PARAM_INT);
                $sth->execute();
                if($sth->rowCount() == 1) {
                    $this->notifyUser("User verified by admin", "");
                    return true;
                } else {
                    $this->notifyUser("Failed to verify user", "");
                    return false;
                }
            } catch (Exception $e) {
                $this->notifyUser("Failed to verify user", $e->getMessage());
                return false;
            }
        } else {return false; }
    }
    // END VERIFY USER BY ADMIN


    // DELETE USER
    public function deleteUser($userID) : bool {
        try
        {
            $stmt = $this->dbase->prepare("DELETE FROM Users WHERE userID = :userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()==1) {
                $this->notifyUser( "User deleted", "");
                return true;
            } else {
                $this->notifyUser( "Failed to delete user!", "");
                return false;
            }
        }
        catch (Exception $e) {
            $this->notifyUser( "Failed to delete user!", $e->getMessage());
            return false;
        }
    }
    // END DELETE USER


    // EDIT USER
    public function editUser(User $user) : bool {
        $userID = $user->getUserId();
        $username = $this->request->request->get('username', $user->getUsername());
        $firstName = $this->request->request->get('firstName', $user->getFirstName());
        $lastName = $this->request->request->get('lastName', $user->getLastName());
        $emailAddress = $this->request->request->get('emailAddress', $user->getEmailAddress());
        $password = $this->request->request->get('password', $user->getPassword());
        $address = $this->request->request->get('address', $user->getAddress());
        $city = $this->request->request->get('city', $user->getCity());
        $zipCode = $this->request->request->get('zipCode', $user->getZipCode());
        $phoneNumber = $this->request->request->get('phoneNumber', $user->getPhoneNumber());
        $mobileNumber = $this->request->request->get('mobileNumber', $user->getMobileNumber());
        $IMAddress = $this->request->request->get('IMAddress', $user->getIMAddress());
        $status = $this->request->request->get('status', $user->getStatus());
        $isTemporary = $this->request->request->getInt('isTemporary', $user->isTemporary());
        $isProjectLeader = $this->request->request->getInt('isProjectLeader', $user->isProjectLeader());
        $isGroupLeader = $this->request->request->getInt('isGroupLeader', $user->isGroupLeader());
        if (!$this->isUsernameAvailable($user, $username)) {
            $this->notifyUser("Failed to edit, username was already taken");
            return false;
        }
        if (!$this->isEmailAvailable($user, $emailAddress)) {
            {
                $this->notifyUser("Failed to edit, email was already taken");
                return false;
            }
        }
        try {
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("update Users set username = :username, firstName = :firstName, 
    lastName = :lastName, emailAddress = :emailAddress, address = :address, city = :city, zipCode = :zipCode, 
    phoneNumber = :phoneNumber, mobileNumber = :mobileNumber, IMAddress = :IMAddress, password = :password, 
    isTemporary = :isTemporary, isProjectLeader = :isProjectLeader, isGroupLeader = :isGroupLeader, status = :status, isAdmin = :isADmin WHERE userID = :userID;");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
            $sth->bindParam(':lastName',  $lastName, PDO::PARAM_STR);
            $sth->bindParam(':emailAddress', $emailAddress, PDO::PARAM_STR);
            $sth->bindParam(':address', $address, PDO::PARAM_STR);
            $sth->bindParam(':city', $city, PDO::PARAM_STR);
            $sth->bindParam(':zipCode',  $zipCode, PDO::PARAM_STR);
            $sth->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            $sth->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
            $sth->bindParam(':IMAddress', $IMAddress, PDO::PARAM_STR);
            $sth->bindParam(':password', $hash, PDO::PARAM_STR);
            $sth->bindParam(':isTemporary', $isTemporary, PDO::PARAM_INT);
            $sth->bindParam(':isProjectLeader', $isProjectLeader, PDO::PARAM_INT);
            $sth->bindParam(':isGroupLeader', $isGroupLeader, PDO::PARAM_INT);
            $sth->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser('User details changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change user details', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change user details", $e->getMessage());
            return false;
        }
    }
    // END EDIT USER


    // EDIT OTHER USER
    public function editOtherUser(User $user) : bool {
        $userID = $user->getUserId();
        $password = $this->request->request->get('password', $user->getPassword());
        $address = $this->request->request->get('address', $user->getAddress());
        $city = $this->request->request->get('city', $user->getCity());
        $zipCode = $this->request->request->get('zipCode', $user->getZipCode());
        $phoneNumber = $this->request->request->get('phoneNumber', $user->getPhoneNumber());
        $mobileNumber = $this->request->request->get('mobileNumber', $user->getMobileNumber());
        $IMAddress = $this->request->request->get('IMAddress', $user->getIMAddress());
        $status = $this->request->request->get('status', $user->getStatus());
        $isTemporary = $this->request->request->getInt('isTemporary', $user->isTemporary());
        $isAdmin = $this->request->request->getInt('isAdmin', $user->isAdmin());
        try {
            $sth = $this->dbase->prepare("update Users set address = :address, city = :city, zipCode = :zipCode, 
    phoneNumber = :phoneNumber, mobileNumber = :mobileNumber, IMAddress = :IMAddress, isTemporary = :isTemporary, 
                 status = :status, isAdmin = :isAdmin WHERE userID = :userID;");
            $sth->bindParam(':address', $address, PDO::PARAM_STR);
            $sth->bindParam(':city', $city, PDO::PARAM_STR);
            $sth->bindParam(':zipCode',  $zipCode, PDO::PARAM_STR);
            $sth->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            $sth->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
            $sth->bindParam(':IMAddress', $IMAddress, PDO::PARAM_STR);
            $sth->bindParam(':isTemporary', $isTemporary, PDO::PARAM_INT);
            $sth->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser('User details changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change user details', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change user details", $e->getMessage());
            return false;
        }
    }
    // END EDIT OTHER USER


    // EDIT MY PROFILE
    public function editMyProfile(User $user) : bool {
        $userID = $user->getUserId();
        $username = $this->request->request->get('username', $user->getUsername());
        $firstName = $this->request->request->get('firstName', $user->getFirstName());
        $lastName = $this->request->request->get('lastName', $user->getLastName());
        $emailAddress = $this->request->request->get('emailAddress', $user->getEmailAddress());
        $password = $this->request->request->get('password', $user->getPassword());
        $address = $this->request->request->get('address', $user->getAddress());
        $city = $this->request->request->get('city', $user->getCity());
        $zipCode = $this->request->request->get('zipCode', $user->getZipCode());
        $phoneNumber = $this->request->request->get('phoneNumber', $user->getPhoneNumber());
        $mobileNumber = $this->request->request->get('mobileNumber', $user->getMobileNumber());
        $IMAddress = $this->request->request->get('IMAddress', $user->getIMAddress());
        $status = $this->request->request->get('status', $user->getStatus());
        $isTemporary = $this->request->request->getInt('isTemporary', $user->isTemporary());
        $isProjectLeader = $this->request->request->getInt('isProjectLeader', $user->isProjectLeader());
        $isGroupLeader = $this->request->request->getInt('isGroupLeader', $user->isGroupLeader());
        if (!$this->isUsernameAvailable($user, $username)) {
            $this->notifyUser("Failed to edit, username was already taken", "");
            return false;
        }
        if (!$this->isEmailAvailable($user, $emailAddress)) {
            {
                $this->notifyUser("Failed to edit, email was already taken", "");
                return false;
            }
        }
        try {
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("update Users set username = :username, firstName = :firstName, 
    lastName = :lastName, emailAddress = :emailAddress, address = :address, city = :city, zipCode = :zipCode, 
    phoneNumber = :phoneNumber, mobileNumber = :mobileNumber, IMAddress = :IMAddress, password = :password, 
    isTemporary = :isTemporary, isProjectLeader = :isProjectLeader, isGroupLeader = :isGroupLeader, status = :status WHERE userID = :userID;");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':firstName', $firstName, PDO::PARAM_STR);
            $sth->bindParam(':lastName',  $lastName, PDO::PARAM_STR);
            $sth->bindParam(':emailAddress', $emailAddress, PDO::PARAM_STR);
            $sth->bindParam(':address', $address, PDO::PARAM_STR);
            $sth->bindParam(':city', $city, PDO::PARAM_STR);
            $sth->bindParam(':zipCode',  $zipCode, PDO::PARAM_STR);
            $sth->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            $sth->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
            $sth->bindParam(':IMAddress', $IMAddress, PDO::PARAM_STR);
            $sth->bindParam(':password', $hash, PDO::PARAM_STR);
            $sth->bindParam(':isTemporary', $isTemporary, PDO::PARAM_INT);
            $sth->bindParam(':isProjectLeader', $isProjectLeader, PDO::PARAM_INT);
            $sth->bindParam(':isGroupLeader', $isGroupLeader, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser('User details changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change user details', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change user details", $e->getMessage());
            return false;
        }
    }
    // END EDIT MY PROFILE


    // EDIT USERNAME
    public function editMyUsername(User $user) : bool {
        $userID = $user->getUserId();
        $username = $this->request->request->get('username', $user->getUsername());
        if (!$this->isUsernameAvailable($user, $username)) {
            $this->notifyUser("Failed to edit, username was already taken", "");
            return false;
        }
        try {
            $sth = $this->dbase->prepare("update Users set username = :username WHERE userID = :userID;");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser('Username changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change username', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change username", $e->getMessage());
            return false;
        }
    }
    // END EDIT USERNAME

    // EDIT EMAIL ADDRESS
    public function editMyEmailAddress(User $user) : bool {
        $userID = $user->getUserId();
        $emailAddress = $this->request->request->get('emailAddress', $user->getEmailAddress());
        if (!$this->isEmailAvailable($user, $emailAddress)) {
            {
                $this->notifyUser("Failed to edit, email was already taken", "");
                return false;
            }
        }
        try {
            $sth = $this->dbase->prepare("update Users set emailAddress = :emailAddress WHERE userID = :userID;");
            $sth->bindParam(':emailAddress', $emailAddress, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser('Email address changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change email address', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change email address", $e->getMessage());
            return false;
        }
    }
    // END EDIT EMAIL ADDRESS

    // EDIT PASSWORD
    public function editPassword(User $user) : bool {
        $userID = $user->getUserId();
        $password = $this->request->request->get('password', $user->getPassword());
        try {
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("update Users set password = :password WHERE userID = :userID;");
            $sth->bindParam(':password', $hash, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser('Password changed', '');
                return true;
            } else {
                $this->notifyUser('Failed to change password', "");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change password", $e->getMessage());
            return false;
        }
    }
    // END EDIT PASSWORD


    // CHECK IF USERNAME IS AVAILABLE (PRIVATE FUNCTION)
    private function isUsernameAvailable(User $user, string $newUsername) : bool {
        if ($user->getUsername() == $newUsername) {
            return true;
        }
        else {
            try {
                $stmt = $this->dbase->prepare("SELECT count(*) as cntUser FROM Users WHERE username = :newUsername");
                $stmt->bindParam(':newUsername', $newUsername, PDO::PARAM_STR);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                if($count > 0){
                    return false;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->notifyUser("Something went wrong with isUserNameAvailable()", $e->getMessage());
                return false;
            }
        }
    }
    // END CHECK IF USERNAME IS AVAILABLE (PRIVATE FUNCTION)

    // CHECK IF EMAIL ADDRESS IS AVAILABLE (PRIVATE FUNCTION)
    private function isEmailAvailable(User $user, String $newEmail) : bool {
        if ($user->getEmailAddress() == $newEmail) {
            return true;
        }
        else {
            try {
                $stmt = $this->dbase->prepare("SELECT count(*) as cntUser FROM Users WHERE emailAddress = :newEmail");
                $stmt->bindParam(':newEmail', $newEmail, PDO::PARAM_STR);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                if($count > 0){
                    return false;
                } else {
                    return true;
                }
            } catch (Exception $e) {
                $this->notifyUser("Something went wrong with isEmailAvailable()", $e->getMessage());
            }
        }
    }
    // END CHECK IF EMAIL ADDRESS AVAILABLE (PRIVATE FUNCTION)


    // GET USER
    public function getUser ($userID) : User {
        try
        {
            $stmt = $this->dbase->prepare("SELECT * FROM Users WHERE userID=:userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if($user = $stmt->fetchObject('User')) {
                return $user;
            }
            else {
                $this->notifyUser("User not found", "");
                return new User();
            }
        }
        catch(Exception $e) { $this->notifyUser("Something went wrong!", $e->getMessage());
            return new User();
        }
    }
    // END GET USER

/*
    // GET ALL USERS
    public function getAllUsers($colName, $isAdmin = null,
                                $isProjectLeader = null, $isGroupLeader = null, $isTemporary = null) : array {
        $allUsers = null;
        try{
           // $colName = "`".str_replace("`","``",$colName)."`";
            $query   = "SELECT * FROM Users ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                $this->notifyUser("User not found", "");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllUsers()", $e->getMessage());
            return array();
        }
        return array();
    }
    // END GET ALL USERS
*/

    // GET ALL USERS
    public function getAllUsers($colName) : array {
        $allUsers = null;
        try{
            // $colName = "`".str_replace("`","``",$colName)."`";
            $query   = "SELECT * FROM Users ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                $this->notifyUser("User not found", "");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllUsers()", $e->getMessage());
            return array();
        }
        return array();
    }
    // END GET ALL USERS



    // GET ALL CUSTOMERS
    public function getAllCustomers($colName) : array {
        $allUsers = null;
        try{
            // $colName = "`".str_replace("`","``",$colName)."`";
            $query   = "SELECT * FROM Users WHERE isCustomer = 1 ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                $this->notifyUser("User not found", "");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllCustomers()", $e->getMessage());
            return array();
        }
        return array();
    }
    // END GET ALL CUSTOMERS

    // GET ALL EMPLOYEES
    public function getAllEmployees($colName) : array {
        $allUsers = null;
        try{
            // $colName = "`".str_replace("`","``",$colName)."`";
            $query   = "SELECT * FROM Users WHERE isCustomer = 0 ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                $this->notifyUser("User not found", "");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllEmployees()", $e->getMessage());
            return array();
        }
        return array();
    }
    // END GET ALL EMPLOYEES





/*

    public function sendEmail(string $email) : bool {
        $ch = curl_init();
        //Koden for å hente URL adresse er tatt og modifisert fra https://www.javatpoint.com/how-to-get-current-page-url-in-php
        if($this->request->server->get('HTTPS') === 'on')
            $url = "https://";
        else
            $url = "http://";
        // Append the host(domain name, ip) to the URL.
        $url .= $this->request->server->get('HTTP_HOST');
        // Append the requested resource location to the URL
        $url .= dirname($this->request->server->get('PHP_SELF'));
        $url .= "/verify.php";
        $timestamp = time();

        $id = md5(uniqid(rand(), 1));
        try{
            $sth = $this->dbase->prepare("update Users set verCode = :id, verified = 0, timestamp = :timestamp where email = :email;");
            $sth->bindParam(':email', $email, PDO::PARAM_STR);
            $sth->bindParam(':id',  $id, PDO::PARAM_STR);
            $sth->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Email sent, check your inbox for verification","");
            } else {
                $this->notifyUser("Failed to send verification email, email not found","");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to send verification email","");
            return false;
        }
        curl_setopt($ch, CURLOPT_URL, "https://kark.uit.no/internett/php/mailer/mailer.php?address=".$email."&url=".$url ."?id=". $id);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return true;
    } //END send Email





    public function verifyUser() : bool {
        $timeLimit = 86400;  //86,400 seconds is one day

        if($id = $this->request->query->get('id')) {
            try {
                $sth = $this->dbase->prepare("select timestamp from Users where vercode = :id");
                $sth->bindParam(':id', $id, PDO::PARAM_STR);
                $sth->execute();
                $timestamp = $sth->fetchColumn();
                if (time() - $timestamp > $timeLimit) {
                    $this->notifyUser("Verification code has expired", "");
                    return false;
                } else {
                    $sth = $this->dbase->prepare("update Users set verified = 1 where verCode = :id");
                    $sth->bindParam(':id', $id, PDO::PARAM_STR);
                    $sth->execute();
                    if($sth->rowCount() == 1) {
                        $this->notifyUser("Email verified", "");
                        return true;
                    }
                    else {
                        $this->notifyUser("Failed to verify email", "");
                        return false;
                    }
                }


            } catch (Exception $e) {
                $this->notifyUser("Failed to verify email", "");
                return false;
            }
        } return false;
    }
*/

}

?>