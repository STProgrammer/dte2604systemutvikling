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



    private function notifyUser($strHeader, $strMessage = null)
    {
        //$this->session->getFlashBag()->clear();
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
        $userType = $this->request->request->getInt('userType');
        //Bare admin kan registrere admin
        if (!$user->isAdmin() && $userType >= 3 | $userType < 0) {
            $userType = 2;
        }
        $status = $userType == 0 ? "N/A": "Fri";
        $isVerifiedByAdmin = $user->isAdmin() ? 1:0;
        try{
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("insert into Users (username, firstName, lastName, emailAddress, address, city, zipCode, phoneNumber, mobileNumber, IMAddress, password, dateRegistered, userType, isVerifiedByAdmin, 
                   isEmailVerified, status) values (:username, :firstName, :lastName, :emailAddress, :address, :city, :zipCode, :phoneNumber, :mobileNumber, :IMAddress, :password, NOW(), :userType, :isVerifiedByAdmin, 1, :status);");
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
            $sth->bindParam(':userType',  $userType, PDO::PARAM_INT);
            $sth->bindParam(':isVerifiedByAdmin', $isVerifiedByAdmin, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Ny bruker ble registrert");
                return true;
            } else {
                $this->notifyUser("Feil ved registrering av ny bruker!");
                return false;
            }
            /*if ($this->sendEmail($email)) { $this->notifyUser("User registered", "");}
            else {$this->notifyUser("Failed to send email to verify!", ""); }*/
        } catch (Exception $e) {
            $this->notifyUser("Feil ved registrering av ny bruker!");
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
                    $this->notifyUser("Bruker verifisert av admin");
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $this->notifyUser("En feil oppstod, bruker ble ikke verifisert!");
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
                $this->notifyUser( "Bruker slettet");
                return true;
            } else {
                $this->notifyUser( "Fikk ikke slette bruker!");
                return false;
            }
        }
        catch (Exception $e) {
            $this->notifyUser( "Feil ved sletting av bruker!");
            return false;
        }
    }
    // END DELETE USER



    // EDIT OTHER USER
    public function editOtherUser(User $user) : bool {
        $userID = $user->getUserId();
        $address = $this->request->request->get('address', $user->getAddress());
        $city = $this->request->request->get('city', $user->getCity());
        $zipCode = $this->request->request->get('zipCode', $user->getZipCode());
        $phoneNumber = $this->request->request->get('phoneNumber', $user->getPhoneNumber());
        $mobileNumber = $this->request->request->get('mobileNumber', $user->getMobileNumber());
        $IMAddress = $this->request->request->get('IMAddress', $user->getIMAddress());
        $status = $this->request->request->get('status', $user->getStatus());
        $userType = $this->request->request->getInt('userType', $user->getUserType());
        //Kunde skal ikke få bli arbeider eller vica versa
        if ($user->isCustomer()) {
            $userType = 0;
            $status = "N/A";
        } else if ($userType == 0) {
            $userType = $user->getUserType();
        }
        try {
            $sth = $this->dbase->prepare("update Users set address = :address, city = :city, zipCode = :zipCode, 
    phoneNumber = :phoneNumber, mobileNumber = :mobileNumber, IMAddress = :IMAddress, userType = :userType, 
                 status = :status WHERE userID = :userID;");
            $sth->bindParam(':address', $address, PDO::PARAM_STR);
            $sth->bindParam(':city', $city, PDO::PARAM_STR);
            $sth->bindParam(':zipCode',  $zipCode, PDO::PARAM_STR);
            $sth->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            $sth->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
            $sth->bindParam(':IMAddress', $IMAddress, PDO::PARAM_STR);
            $sth->bindParam(':userType', $userType, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Brukerdetlajer ble endret!");
                return true;
            } else {
                $this->notifyUser("Feilet endring av brukerdetaljer!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feilet endring av brukerdetaljer!");
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
        $isProjectLeader = $this->request->request->getInt('isProjectLeader', $user->isProjectLeader());
        $isGroupLeader = $this->request->request->getInt('isGroupLeader', $user->isGroupLeader());
        if (!$this->isUsernameAvailable($user, $username)) {
            $this->notifyUser("Feilet ved endring, brukernavnet var allerede tatt!");
            return false;
        }
        if (!$this->isEmailAvailable($user, $emailAddress)) {
            {
                $this->notifyUser("Feilet ved endring, epost addressen var allerede tatt!");
                return false;
            }
        }
        try {
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("update Users set username = :username, firstName = :firstName, 
                        lastName = :lastName, emailAddress = :emailAddress, address = :address, city = :city, zipCode = :zipCode, 
                        phoneNumber = :phoneNumber, mobileNumber = :mobileNumber, IMAddress = :IMAddress, password = :password, 
                        isProjectLeader = :isProjectLeader, isGroupLeader = :isGroupLeader, status = :status WHERE userID = :userID;");
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
            $sth->bindParam(':isProjectLeader', $isProjectLeader, PDO::PARAM_INT);
            $sth->bindParam(':isGroupLeader', $isGroupLeader, PDO::PARAM_INT);
            $sth->bindParam(':status', $status, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Brukerdetaljer ble endret");
                return true;
            } else {
                $this->notifyUser("Feilet endring av brukerdetaljer!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feilet endring av brukerdetaljer!");
            return false;
        }
    }
    // END EDIT MY PROFILE


    // EDIT USERNAME
    public function editMyUsername(User $user) : bool {
        $userID = $user->getUserId();
        $username = $this->request->request->get('username', $user->getUsername());
        if (!$this->isUsernameAvailable($user, $username)) {
            $this->notifyUser("Feilet endring, brukernavn var allerede tatt!");
            return false;
        }
        try {
            $sth = $this->dbase->prepare("update Users set username = :username WHERE userID = :userID;");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Brukernavn ble endret");
                return true;
            } else {
                $this->notifyUser("Feilet endring av brukernavn!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feilet endring av brukernavn!");
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
                $this->notifyUser("Feilet endring, epost addressen var allerede tatt");
                return false;
            }
        }
        try {
            $sth = $this->dbase->prepare("update Users set emailAddress = :emailAddress WHERE userID = :userID;");
            $sth->bindParam(':emailAddress', $emailAddress, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Epost addressen ble endret");
                return true;
            } else {
                $this->notifyUser("Feilet endring av epost addressen");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feilet endring av epost addressen");
            return false;
        }
    }
    // END EDIT EMAIL ADDRESS

    // EDIT PASSWORD
    public function editPassword($userId) : bool {
        $userID = $userId;
        $password = $this->request->request->get('password');
        try {
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $sth = $this->dbase->prepare("update Users set password = :password WHERE userID = :userID;");
            $sth->bindParam(':password', $hash, PDO::PARAM_STR);
            $sth->bindParam(':userID', $userID, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Passordet er endret");
                return true;
            } else {
                $this->notifyUser("Feilet endring av passord");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feilet endring av passord");
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
            }
        }
    }
    // END CHECK IF EMAIL ADDRESS AVAILABLE (PRIVATE FUNCTION)


    // GET USER
    public function getUser ($userID) {
        try
        {
            $stmt = $this->dbase->prepare("SELECT * FROM Users WHERE userID=:userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if($user = $stmt->fetchObject('User')) {
                return $user;
            }
            else {
                return null;
            }
        }
        catch(Exception $e) { $this->notifyUser("En feil oppstod ved henting av bruker!");
            return null;
        }
    }
    // END GET USER


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
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod ved henting av alle brukere");
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
            $query   = "SELECT * FROM Users WHERE userType = 0 ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod ved henting av registrerte kunder");
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
            $query   = "SELECT * FROM Users WHERE userType > 0 ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod ved henting av alle ansatte");
            return array();
        }
        return array();
    }
    // END GET ALL EMPLOYEES


    // GET SINGLE USER STATISTICS
    public function getUserStatistics($userID): array
    {
        try {
            $stmt = $this->dbase->prepare("SELECT *, 
                IFNULL(sumTW, '00:00:00') AS sumTW, 
                IFNULL(sumThisMonth, '00:00:00') AS sumThisMonth FROM TaskCategories
                LEFT JOIN (SELECT DISTINCT CASE WHEN Hours.whoWorked = :userID THEN Hours.whoWorked ELSE NULL END as whoWorked, Hours.taskType, Hours.timeWorked, 
                IFNULL(SEC_TO_TIME(SUM(CASE WHEN Hours.stampingStatus = 1 THEN timeWorked ELSE 0 END)), '00:00:00') as sumTW, 
                IFNULL(SEC_TO_TIME(SUM(CASE WHEN Hours.endTime between DATE_FORMAT(NOW(), '%Y-%m-01') AND NOW() AND Hours.stampingStatus = 1 THEN Hours.timeWorked ELSE 0 END)), '00:00:00') as sumThisMonth 
                FROM Hours
                WHERE Hours.whoWorked = :userID OR whoWorked is NULL GROUP BY Hours.taskType) as Hours on Hours.taskType = TaskCategories.categoryName WHERE 1;");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($hours = $stmt->fetchAll()) {
                return $hours;
            } else {
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod ved henting tidsbruk for bruker!");
            return array();
        }
    }


    // GET ALL UNVERIFIED USERS
    public function getUnverifiedUsers($colName) : array {
        $allUsers = null;
        try{
            // $colName = "`".str_replace("`","``",$colName)."`";
            $query   = "SELECT * FROM Users WHERE isVerifiedByAdmin = 0 ORDER BY `$colName` ASC;";
            $stmt = $this->dbase->prepare($query);
            $stmt->execute();
            if($allUsers = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $allUsers;
            }
            else {
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod ved henting av uverifiserte brukere");
            return array();
        }
        return array();
    }
    // END GET ALL UNVERIFIED USERS


}

?>