<?php

class User {
    private $userID;
    private $username;
    private $firstName;
    private $lastName;
    private $address;
    private $zipCode;
    private $city;
    private $phoneNumber;
    private $mobileNumber;
    private $emailAddress;
    private $IMAddress;
    private $dateRegistered;
    private $password;
    private $userType;
    private $isProjectLeader;
    private $isGroupLeader;
    private $isEmailVerified;
    private $isVerifiedByAdmin;
    private $status;
    private $IPAddress;
    private $userAgent;
    private $userHits;


    function __construct(string $username = null, string $ip = null, string $browser = null, array $row = null ) {
        if ($row) {
            $this->userID = $row['userID'];
            $this->username = $username;
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->address = $row['address'];
            $this->zipCode = $row['zipCode'];
            $this->city = $row['city'];
            $this->phoneNumber = $row['phoneNumber'];
            $this->mobileNumber = $row['mobileNumber'];
            $this->emailAddress = $row['emailAddress'];
            $this->IMAddress = $row['IMAddress'];
            $this->dateRegistered = $row['dateRegistered'];
            $this->password = $row['password'];
            $this->userType = $row['userType'];
            $this->isProjectLeader = $row['isProjectLeader'];
            $this->isGroupLeader = $row['isGroupLeader'];
            $this->isEmailVerified = $row['isEmailVerified'];
            $this->isVerifiedByAdmin = $row['isVerifiedByAdmin'];
            $this->status = $row['status'];
            $this->IPAddress = $ip;
            $this->userAgent = $browser;
            $this->userHits = 0;
        }
        else {

        }

    }

    public function verifyUser($request) {
        //$request = Request::createFromGlobals();
        if(($this->IPAddress == $request->server->get('REMOTE_ADDR')) && ($this->userAgent == $request->server->get('HTTP_USER_AGENT') )){
            $this->userHits++;
            return true;
        }
        else
            return false;
    }

    public static function login(PDO $db,  \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session) {
        $username = $request->request->get('username');
        try {
            $stmt = $db->prepare("SELECT * FROM Users WHERE `Username`= :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if (password_verify($request->request->get('password'), $row['password'])) {
                    $session->set('loggedin', true);
                    $ip = $request->server->get('REMOTE_ADDR');
                    $browser = $request->server->get('HTTP_USER_AGENT');
                    $session->set('User', new User($request->request->get('username'), $ip, $browser, $row));
                    return true;
                } else {
                    $session->getFlashBag()->add('header', "Wrong username or password");
                    return false;
                }
            }  else {
                $session->getFlashBag()->add('header', "Wrong username or password");
                return false;
            }
        } catch (Exception $e) {
            $session->getFlashBag()->add('message', $e->getMessage());
        }
    }


    public function getUserId() { return $this->userID; }
    public function getUsername() { return $this->username; }
    public function getFirstName() { return $this->firstName; }
    public function getLastName() { return $this->lastName; }
    public function getAddress() { return $this->address; }
    public function getZipCode() { return $this->zipCode; }
    public function getCity() { return $this->city; }
    public function getPhoneNumber() { return $this->phoneNumber; }
    public function getMobileNumber() { return $this->mobileNumber; }
    public function getEmailAddress() { return $this->emailAddress; }
    public function getIMAddress() { return $this->IMAddress; }
    public function getDateRegistered() { return $this->dateRegistered; }
    public function getPassword() { return $this->password; }
    public function getStatus() { return $this->status; }
    public function getIPAddress(): string { return $this->IPAddress; }
    public function getUserAgent(): string { return $this->userAgent; }
    public function getUserHits(): int { return $this->userHits; }
    public function getUserType(): int { return $this->userType; }

    public function setUserId($userId) { $this->userID = $userId; }
    public function setUsername($username) { $this->username = $username; }
    public function setFirstName($firstName) { $this->firstName = $firstName; }
    public function setLastName($lastName) { $this->lastName = $lastName; }
    public function setAddress($address) { $this->address = $address; }
    public function setZipCode($zipCode) { $this->zipCode = $zipCode; }
    public function setCity($city) { $this->city = $city; }
    public function setPhoneNumber($phoneNumber) { $this->phoneNumber = $phoneNumber; }
    public function setMobileNumber($mobileNumber) { $this->mobileNumber = $mobileNumber; }
    public function setEmailAddress($emailAddress) { $this->emailAddress = $emailAddress; }
    public function setIMAddress($IMAddress) { $this->IMAddress = $IMAddress; }
    public function setDateRegistered($dateRegistered) { $this->dateRegistered = $dateRegistered; }
    public function setPassword($password) { $this->password = $password; }
    public function setStatus($status) { $this->status = $status; }
    public function setIPAddress(string $IPAddress) { $this->IPAddress = $IPAddress; }
    public function setUserAgent(string $userAgent) { $this->userAgent = $userAgent; }
    public function setUserHits(int $userHits) { $this->userHits = $userHits; }
    public function setUserType(int $userType) { $this->userType = $userType; }

    public function isAdmin() : bool { return ($this->userType == 3); }
    public function isProjectLeader() { return $this->isProjectLeader; }
    public function isGroupLeader() { return $this->isGroupLeader; }
    public function isTemporary() : bool { return ($this->userType == 1); }
    public function isUser() : bool { return ($this->userType ==2 || $this->userType == 1); }
    public function isEmployee() : bool { return ($this->userType ==2 || $this->userType == 1);}
    public function isCustomer() : bool { return ($this->userType == 0); }

    public function hasActiveTimeregistration() : bool {
        /*
         * PSEUDO JS:
         * const timeregistration = getFromDB(Hour);
         * const filtered = timeregistration.filter((timeregistration) => timeregistration.whoWorked === this.userID && timeregistration.isActivated )
         * */
    }

    public function setProjectLeader($isProjectLeader) { $this->isProjectLeader = $isProjectLeader; }
    public function setGroupLeader($isGroupLeader) { $this->isGroupLeader = $isGroupLeader; }

    public function isEmailVerified() { return $this->isEmailVerified; }
    public function setEmailVerified($isEmailVerified) { $this->isEmailVerified = $isEmailVerified; }

    public function isVerifiedByAdmin() { return $this->isVerifiedByAdmin; }
    public function setVerifiedByAdmin($isVerifiedByAdmin) { $this->isVerifiedByAdmin = $isVerifiedByAdmin; }
    }
?>