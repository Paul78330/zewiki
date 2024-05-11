<?php

use LDAP\Result;

require_once "../app/models/entities/User.php";
require_once "../app/models/entities/Folder.php";

class DatabaseManager{
# -------------------- Properties --------------------
    
    private PDO $pdo;

# -------------------- Constructor --------------------
    public function __construct() {
        $this->pdo = new PDO("mysql:host=192.168.0.50;dbname=zewiki;charset=utf8;port=3306", "zewikiphpuser","zepdfife85*-zd");
    }


# -------------------- Methods --------------------


public function export()
{
    # Prepared Statements 
    $statement = $this->pdo->prepare("INSERT INTO logs (log_category, log_level ,log_ip ,log_user, log_message) VALUES (?,?,?,?,?)");

    # Statement execution with params
    $statement->execute([$category, $level ,$ip, $user, $message ]);
}


# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # log informations 
    public function logData($category,$level,$message) : bool {

        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset ($_SESSION['user'])){
            $user = ($_SESSION['user'])->getFirstName() . " " . ($_SESSION['user'])->getLastname();
        }
        else{
            $user = 'inconnu';
        }

        try{
            # Prepared Statements 
            $statement = $this->pdo->prepare("INSERT INTO logs (log_category, log_level ,log_ip ,log_user, log_message) VALUES (?,?,?,?,?)");

            # Statement execution with params
            $statement->execute([$category, $level ,$ip, $user, $message ]);
        }
        catch(PDOException $e){
            return false;
            echo "loging error : " . $e->getMessage();
            exit();
        }
        return true;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function setLastConnectionDate($email){

        $date = date("Y-m-d H:i:s");
 
        try{
            $statement = $this->pdo->prepare("UPDATE users set user_last_connection = ? where user_email = ?");
            $result = $statement->execute([$date,$email]);
        }
        catch(PDOException $e){
            return false;
            echo "loging error : " . $e->getMessage();
            exit();
        }
        return true;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Insert user in Database --------------------
    public function insertUser(User $user):bool {

        try
        {
            # Prepared Statements 
            $statement = $this->pdo->prepare("INSERT INTO users (user_firstname,user_lastname,user_password,user_email) VALUES (?,?,?,?)");

            # Statement execution with params
            $statement->execute([$user->getFirstname(),$user->getLastname(),$user->getPassword(), $user->getEmail()]);
        }
        catch(PDOException $e)
        {
            $this->logData("User",3,$e->getMessage());
            echo "Insert user error : " . $e->getMessage();
            exit();
        }

        # User creation ok 
            
        # create the new user folder by Email
        $result = $this->insertHomeFolder($user->getEmail());

        if ($result)
        {
            $this->logData("folder",1,"création du dossier utilisateur de " . $user->getFirstname() . " " . $user->getLastname() );
        }
        else
        {
            $this->logData("folder",1,"échec de création du dossier utilisateur de  " . $user->getFirstname() . " " . $user->getLastname());
        }
        return $result;

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Insert home user folder in Database (during creating user process)
    public function insertHomeFolder($email):bool {

        $id = $this->getUserIdByEmail($email);

        try
        {
            # Prepared Statements 
            $statement = $this->pdo->prepare("INSERT INTO folders (folder_left_edge, folder_right_edge, folder_name, user_id) VALUES (?,?,?,?)");

            # Statement execution with params
            $result = $statement->execute([1,2,'Home', $id]);
        }
        catch(PDOException $e)
        {
            $this->logData("folder",3,"echec de création dossier home pour le compte " . $email );
            echo "Insert user error : " . $e->getMessage();
            exit();
        }
    
        # Result return true if ok 
        $this->logData("folder",1,"création du dossier home pour le compte " . $email );
        return $result;
    }


# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Get user id by user email (to add home folder)
    public function getUserIdByEmail($email):mixed {

        try
        {
            # Prepared Statements 
            $statement = $this->pdo->prepare("SELECT user_id FROM users WHERE user_email = ?");

            # Statement execution with params
            $statement->execute([$email]);

            # Get result from sql
            $result = $statement->fetch();
        }
        catch(PDOException $e)
        {
            echo "Get user by email : " . $e->getMessage();
            exit();
        }
        # Return id
        $this->logData('user',1," getUserIdByEmail : email = " . $email. " id retourné = " . $result['user_id'] );
        return $result['user_id'];
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Verify if user is admin

    public function isAdmin($userId):bool
    {
        try
        {
            # Prepared Statements
            $statement = $this->pdo->prepare("SELECT user_isadmin FROM users WHERE user_id = ?");

            # Statement execution with params
            $statement->execute([$userId]);
            $result = $statement->fetch();
            if($result['user_isadmin'] == 1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch(PDOException $e)
        {
            echo "get email user error : " . $e->getMessage();
            exit();
        }
    }



# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Verify email exists in Database
    public function getUserByEmail(string $user_email):mixed {

        try
        {
            # Prepared Statements
            $statement = $this->pdo->prepare("SELECT user_email FROM users WHERE user_email = ?");

            # Statement execution with params
            $statement->execute([$user_email]);
            $result = $statement->fetch();
        }
        catch(PDOException $e)
        {
            echo "get email user error : " . $e->getMessage();
            exit();
        }
    
        # Result return all user datas 
        return $result;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Verify user password
    public function verifyPassword(string $user_email, string $user_password):bool{

        try{
            # Prepared Statements 
            $statement = $this->pdo->prepare("SELECT user_password FROM users WHERE user_email = ?");

            # Statement execution with params
            $statement->execute([$user_email]);

            # Get result from sql
            $result = $statement->fetch();
        }
        catch(PDOException $e)
        {
            echo "get password user error : " . $e->getMessage();
            exit();
        }

        if (password_verify($user_password,$result["user_password"]))
        {
            return true;
        }
        return false;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Verify if account is active 
    public function is_active($email):bool
    {
        try{
            # Prepared Statements 
            $statement = $this->pdo->prepare("SELECT is_active FROM users WHERE user_email = ?");

            # Statement execution with params
            $statement->execute([$email]);
            $result = $statement->fetch();

            if($result['is_active'] == 1){
                return true;
            }
            else{
                return false;
            }
    
        }
        catch(PDOException $e)
        {
            echo "get is_active : " . $e->getMessage();
            exit();
        }
    }

    # -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Add user informations in user session
    public function getUserDatas(string $user_email):bool{

        try{
            # Prepared Statements 
            $statement = $this->pdo->prepare("SELECT * FROM users WHERE user_email = ?");

            # Statement execution with params
            $statement->execute([$user_email]);

            # Get result from sql
            $result = $statement->fetch();

        }
        catch(PDOException $e)
        {
            echo "get user datas error : " . $e->getMessage();
            exit();
        }

        # Create user object if statement ok 
        if($result){
            # Create defaut user from contructor
            $user = new User($result['user_firstname'],$result['user_lastname'],$result['user_email']);
            
            # Add more informations ( password not needed)
            $user->setId($result['user_id']);
            $user->setIsAdmin($result['user_isadmin']);
            $user->setCreationDate($result['user_creationdate']);
            $user->setLastConnectionDate($result['user_last_connection']);

            # Add user objet in user session
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Get nav folders and documents
    public function getTree($sql_view):mixed{
 
        # $sql_view options : 'hometree', 'publictree', sharetree' ( from database views)

        # case home tree
        if ($sql_view == 'hometree'){

            try
            {
                # Prepared Statements, getting items from hometree
                $statement = $this->pdo->prepare("SELECT * FROM hometree where user_id = ?");
                # Statement execution 
                $statement->execute([$_SESSION['user']->getId()]);
                # return data with colum name
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
            catch(PDOException $e)
            {
                echo "Zewiki -> getTree  error : ". $sql_view . ' ' . $e->getMessage();
                exit();
            }
        }

        # case common and share tree
        else
        {
            try
            {
                # Prepared Statements, getting items from view PublicTree 
                $statement = $this->pdo->prepare("SELECT * FROM $sql_view");
                # Statement execution 
                $statement->execute();
                # return data with colum name
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
            catch(PDOException $e)
            {
                echo "Zewiki -> getTree  error : ". $sql_view . ' ' . $e->getMessage();
                exit();
            }
        }
    }


# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Add item (folder or document) by Parentid in database
    public function addItemByParentId(int $parentId,string $itemName,string $itemType,int $userId,$documentContent=""):mixed
    {
        try
        {
            # Prepared Statements get right_edge from ParentId to define new item left_edge
            $statement = $this->pdo->prepare("SELECT folder_right_edge FROM folders WHERE folder_id = ?");

            # Statement execution with params
            $statement->execute([$parentId]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            echo "addItemByParentId first part error : " . $e->getMessage();
            exit();
        }
        
        if ($result) // right edge parent found
        {
            # Get rightEdge from parent folder
            $parentRightEdge = $result['folder_right_edge'];

            # Start transaction ( all modified or none)
            try
            {
                # Transaction begin :
                $this->pdo->beginTransaction();

                # -- Right edge folders prepared Statements
                $statement = $this->pdo->prepare("UPDATE folders set folder_right_edge = folder_right_edge + 2 where folder_right_edge >= ? and user_id = ?");
                $result = $statement->execute([$parentRightEdge,$userId]);


                # -- Right edge documents prepared Statements
                $statement = $this->pdo->prepare("UPDATE documents set document_right_edge = document_right_edge + 2 where document_right_edge >= ? and user_id = ?");
                $statement->execute([$parentRightEdge,$userId]);

                # -- Left edge folders prepared Statements
                $statement = $this->pdo->prepare("UPDATE folders set folder_left_edge = folder_left_edge + 2 where folder_left_edge >= ? and user_id = ?");
                $statement->execute([$parentRightEdge,$userId]);
                
                # -- Left edge documents prepared Statements
                $statement = $this->pdo->prepare("UPDATE documents set document_left_edge = document_left_edge + 2 where document_left_edge >= ? and user_id = ?");
                $statement->execute([$parentRightEdge,$userId]);
                
                # -- Insert new Item depending itemType
                if($itemType == 'folder')
                {
                    $statement = $this->pdo->prepare("INSERT INTO folders (folder_name,folder_left_edge,folder_right_edge,user_id) VALUES (?,?,?,?)");
                    $result = $statement->execute([$itemName,$parentRightEdge,$parentRightEdge+1,$userId]);
                }
                else
                {
                    $statement = $this->pdo->prepare("INSERT INTO documents (document_name,document_left_edge,document_right_edge,user_id,folder_id,document_content) VALUES (?,?,?,?,?,?)");
                    $result = $statement->execute([$itemName,$parentRightEdge,$parentRightEdge+1,$userId,$parentId,$documentContent]);
                }
              
                # Transaction ok
                $this->pdo->commit();
                # log 
                $this->logdata($itemType,1,"le $itemType $itemName a été ajouté");
            }

            catch(PDOException $e)
            {
                $this->pdo->rollBack();
                echo " addItemByParentId transaction erreur : " . $e->getMessage();
                exit();
            }
        }
        else
        {
            return false;
        }
        return $result;
    }



# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getUserIdFromSource(string $source):int
    {
        switch($source)
        {
            
            case 'home' :
                return ($_SESSION['user'])->getId();

            case 'common':
                return 2;

            case 'shares':
                return 3;
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Delete document by Id from database
    public function deleteDocumentById(int $documentId,$documentSource):bool{

        $source = $this->getUserIdFromSource($documentSource);

        try
        {
            # Prepared Statements get right_edge from document
            $statement = $this->pdo->prepare("SELECT * FROM documents WHERE document_id = ?");
            
            # Statement execution with params
            $statement->execute([$documentId]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $this->logData('document',3,'echec de récupération du document ' . $documentId);
            echo "deleteDocumentById get right_edge error : " . $e->getMessage();
            exit();
        }
        
        if ($result) // right edge found
        {
            $this->logData('document',1,'récupération du document id= ' . $documentId . ' documentRightEdge = ' . $result['document_right_edge'] );
            # Get rightEdge from parent folder
            $documentRightEdge = $result['document_right_edge'];

            # Start transaction ( all modified or none)
            try
            {
                # Transaction begin :
                $this->pdo->beginTransaction();

                # -- remove document
                $statement = $this->pdo->prepare("delete from documents where document_id = ?");
                $statement->execute([$documentId]);

                # -- Right edge folders prepared Statements
                $statement = $this->pdo->prepare("UPDATE folders set folder_right_edge = folder_right_edge - 2 where folder_right_edge >= ? and user_id = ?");
                $statement->execute([$documentRightEdge,$source]);

                # -- Right edge documents prepared Statements
                $statement = $this->pdo->prepare("UPDATE documents set document_right_edge = document_right_edge - 2 where document_right_edge >= ? and user_id = ?");
                $statement->execute([$documentRightEdge,$source]);

                # -- Left edge folders prepared Statements
                $statement = $this->pdo->prepare("UPDATE folders set folder_left_edge = folder_left_edge - 2 where folder_left_edge >= ? and user_id = ?");
                $statement->execute([$documentRightEdge,$source]);
                
                # -- Left edge documents prepared Statements
                $statement = $this->pdo->prepare("UPDATE documents set document_left_edge = document_left_edge - 2 where document_left_edge >= ? and user_id = ?");
                $statement->execute([$documentRightEdge,$source]);
                # Transaction ok
                $this->pdo->commit();
                # log 
                $this->logData('document',1,"Le document " . $result['document_name'] . " à été supprimé");
            }
            catch(PDOException $e)
            {
                $this->pdo->rollBack();
                echo " delete document transaction erreur : " . $e->getMessage();
                exit();
            }
        }
        else
        {
            return false;
        }
        return true;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Delete folder by Id from database
    public function deleteFolderById(int $folderId,$source):bool{

        # Verify the folder is empty (no documents, or no subfolder)

        try
        {
            # Prepared Statements get right_edge from folder
            $statement = $this->pdo->prepare("SELECT (folder_right_edge - folder_left_edge) AS size FROM folders WHERE folder_id = ?;");
            
            # Statement execution with params
            $statement->execute([$folderId]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result['size'] > 1){
                return false;
            }
            
        }
        catch(PDOException $e)
        {
            echo "Get size folder before delete error : " . $e->getMessage();
            exit();
        }

        try
        {
            # Prepared Statements get right_edge from folder
            $statement = $this->pdo->prepare("SELECT * FROM folders WHERE folder_id = ?");
            
            # Statement execution with params
            $statement->execute([$folderId]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            echo "deleteFolderById get right_edge error : " . $e->getMessage();
            exit();
        }
        
        if ($result) // right edge found
        {
            # Get rightEdge from parent folder
            $folderRightEdge = $result['folder_right_edge'];

            # Start transaction ( all modified or none)
            try
            {
                # Transaction begin :
                $this->pdo->beginTransaction();

                # -- remove document
                $statement = $this->pdo->prepare("delete from folders where folder_id = ?");
                $statement->execute([$folderId]);

                # -- Right edge folders prepared Statements
                $statement = $this->pdo->prepare("UPDATE folders set folder_right_edge = folder_right_edge - 2 where folder_right_edge >= ? and user_id = ?");
                $statement->execute([$folderRightEdge,$source]);

                # -- Right edge documents prepared Statements
                $statement = $this->pdo->prepare("UPDATE documents set document_right_edge = document_right_edge - 2 where document_right_edge >= ? and user_id = ?");
                $statement->execute([$folderRightEdge,$source]);

                # -- Left edge folders prepared Statements
                $statement = $this->pdo->prepare("UPDATE folders set folder_left_edge = folder_left_edge - 2 where folder_left_edge >= ? and user_id = ?");
                $statement->execute([$folderRightEdge,$source]);
                
                # -- Left edge documents prepared Statements
                $statement = $this->pdo->prepare("UPDATE documents set document_left_edge = document_left_edge - 2 where document_left_edge >= ? and user_id = ?");
                $statement->execute([$folderRightEdge,$source]);
                # Transaction ok
                $this->pdo->commit();
                # log 
                $this->logData('document',1,"Le folder " . $result['folder_name'] . " à été supprimé");
            }
            catch(PDOException $e)
            {
                $this->pdo->rollBack();
                echo " delete folder transaction erreur : " . $e->getMessage();
                exit();
            }
        }
        else
        {
            return false;
        }
        return true;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Delete folder by Id from database
    public function renameFolder($folderId,$newFolderName):bool
    {
        try
        {
            $statement = $this->pdo->prepare("update folders set folder_name = ? where folder_id = ?");
            $result = $statement->execute([$newFolderName,$folderId]);

        }
        catch(PDOException $e)
        {
            echo " renameFolder  erreur : " . $e->getMessage();
            exit();
        }
        return $result;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Return document content from document_id
    public function getDocumentContentById(int $documentId):mixed
    {
        try
        {
            $statement = $this->pdo->prepare("select document_content from documents where document_id = ?");
            $statement->execute([$documentId]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

        }
        catch(PDOException $e)
        {
            echo " getDocumentById  erreur : " . $e->getMessage();
            exit();
        }
        return $result;
    }

    # -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Update document infos with document_id
    public function updateDocumentById($documentId,$documentContent)
    {
        try
        {
            $statement = $this->pdo->prepare("update documents set document_content = ? where document_id = ?");
            $result = $statement->execute([$documentContent,$documentId]);

        }
        catch(PDOException $e)
        {
            echo " getDocumentById  erreur : " . $e->getMessage();
            exit();
        }
        return $result;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Create a new token row in tokens table 
    public function saveTokenInDatabase($email,$token,$tokenSource) : bool {

        # Verify if email exists
        $validEmail = $this->getUserByEmail($email);
        
        if(!$validEmail){
            # Email invalid
            return false;
        } 
        try
        {
            $statement = $this->pdo->prepare("INSERT INTO tokens (token_value,token_email, token_source) VALUES (?,?,?)");
            $statement->execute([$token, $email,$tokenSource]);
            return true;
        }
        catch(PDOException $e)
        {
            echo " saveTokenInDatabase  erreur : " . $e->getMessage();
            return false;
        }
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getTokenInfos($token) :mixed
    {
        try
        {
            # Prepared Statement get token
            $statement = $this->pdo->prepare("SELECT * FROM tokens WHERE token_value = ?");
            
            # Statement execution with params
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $this->logData('reset password',3,'le jeton n\'existe pas ');
            echo "getTokenInfos : " . $e->getMessage();
            exit();
        }
        return $result;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function setNewPasswordFromToken($password)
    {
        # Get email from address
        if(!isset($_SESSION['token']))
        {
            return false;
        }
        else
        {
            $token = $_SESSION['token'];
            $result =  $this->getTokenInfos($token);
            $email  = $result['token_email'];
            $hashedPassword =  password_hash($password, PASSWORD_DEFAULT);
            # Save new user password 
            try
            {
                $statement = $this->pdo->prepare("UPDATE users set user_password = ? where user_email = ?");
                $statement->execute([$hashedPassword,$email]);
                return true;
    
            }
            catch(PDOException $e)
            {
                #echo " saveResetPasswordRequest  erreur : " . $e->getMessage();
                return false;
            }
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function activeAccountByToken($token)
    {
        # Get email from $token
        try
        {
            # Prepared Statements get right_edge from folder
            $statement = $this->pdo->prepare("SELECT * FROM tokens WHERE token_source = 'activation_account' and token_value = ?");
            
            # Statement execution with params
            $statement->execute([$token]);
            $tokenInfos = $statement->fetch(PDO::FETCH_ASSOC);

            if(!$tokenInfos)
            {
                # Token not found

            }
            else
            {   
                # Token found, active the user account by email
                try
                {
                    $statement = $this->pdo->prepare("update users set is_active = 1 where user_email = ?");
                    $result = $statement->execute([$tokenInfos['token_email']]);
                    return true;
        
                }
                catch(PDOException $e)
                {
                    echo " getDocumentById  erreur : " . $e->getMessage();
                    return false;
                }
                return $result;
            }
        }
        catch(PDOException $e)
        {
            echo "deleteFolderById get right_edge error : " . $e->getMessage();
            return false;
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Get users list for admin panel
    public function getUsersList():mixed
    {
        try
        {
            # Prepared Statement get token
            $statement = $this->pdo->prepare("SELECT * FROM users");
            
            # Statement execution with params
            $statement->execute();
            $users = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $this->logData('admin',3,'$e->getMessage()');
            exit();
        }
        return $users;

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Get logs list for admin panel
    public function getLogsList()
    {
        try
        {
            # Prepared Statement get token
            $statement = $this->pdo->prepare("SELECT * FROM logs");
            
            # Statement execution with params
            $statement->execute();
            $logs = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $this->logData('admin',3,'$e->getMessage()');
            exit();

        }
        return $logs;

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Get tokens list for admin panel
    public function getTokensList()
    {
        try
        {
            # Prepared Statement get token
            $statement = $this->pdo->prepare("SELECT * FROM tokens");
            
            # Statement execution with params
            $statement->execute();
            $tokens = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $this->logData('admin',3,'$e->getMessage()');
            exit();
        }
        return $tokens;

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Disable account from admin panel
    public function user_disable($id):bool
    {
        # Verify if request is from admin user 
        $adminUser = $_SESSION['user']->getId();
        if ($this->isAdmin($adminUser))
        # User is admin 
        {
            try
            {
                $statement = $this->pdo->prepare("UPDATE users set is_active = 0 where user_id = ?");
                $result = $statement->execute([$id]);
                if($result)
                {
                    $this->logData('admin',1,"désactivation du compte" . $id );
                    return true;
                }
    
            }
            catch(PDOException $e)
            {
                $this->logData('admin',3,'$e->getMessage()');
                return false;
                exit();
            }

        }
        else
        {
            $this->logData('admin',1,"demande de désactivation d'un compte depuis un compte non admin");
            return false;
        }
    }

    # -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Enable account from admin panel
    public function user_enable($id):bool
    {
        # Verify if request is from admin user 
        $adminUser = $_SESSION['user']->getId();
        if ($this->isAdmin($adminUser))
        # User is admin 
        {
            try
            {
                $statement = $this->pdo->prepare("UPDATE users set is_active = 1 where user_id = ?");
                $result = $statement->execute([$id]);
                if($result)
                {
                    $this->logData('admin',1,"activation du compte" . $id );
                    return true;
                }
    
            }
            catch(PDOException $e)
            {
                $this->logData('admin',3,'$e->getMessage()');
                return false;
                exit();
            }

        }
        else
        {
            $this->logData('admin',1,"demande d'activation d'un compte depuis un compte non admin");
            return false;
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Enable account from admin panel
    public function user_downgrade($id):bool
    {
        # Verify if request is from admin user 
        $adminUser = $_SESSION['user']->getId();
        if ($this->isAdmin($adminUser))
        # User is admin 
        {
            try
            {
                $statement = $this->pdo->prepare("UPDATE users set user_isadmin = 0 where user_id = ?");
                $result = $statement->execute([$id]);
                if($result)
                {
                    $this->logData('admin',1,"downgrade du compte" . $id );
                    return true;
                }
    
            }
            catch(PDOException $e)
            {
                $this->logData('admin',3,'$e->getMessage()');
                return false;
                exit();
            }

        }
        else
        {
            $this->logData('admin',1,"demande de downgrade d'un compte depuis un compte non admin");
            return false;
        }
    }


    # -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Enable account from admin panel
    public function user_upgrade($id):bool
    {
        # Verify if request is from admin user 
        $adminUser = $_SESSION['user']->getId();
        if ($this->isAdmin($adminUser))
        # User is admin 
        {
            try
            {
                $statement = $this->pdo->prepare("UPDATE users set user_isadmin = 1 where user_id = ?");
                $result = $statement->execute([$id]);
                if($result)
                {
                    $this->logData('admin',1,"upgrade du compte" . $id );
                    return true;
                }
    
            }
            catch(PDOException $e)
            {
                $this->logData('admin',3,'$e->getMessage()');
                return false;
                exit();
            }

        }
        else
        {
            $this->logData('admin',1,"demande d'upgrade d'un compte depuis un compte non admin");
            return false;
        }
    }

        # -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Delete account from admin panel
    public function user_delete($id):bool
    {
        # Verify if request is from admin user 
        $adminUser = $_SESSION['user']->getId();
        if ($this->isAdmin($adminUser))
        # User is admin 
        {
            try
            {   
                # Beginning the transaction all ok or none
                $this->pdo->beginTransaction();

                # Delete all folders for the selected user
                $statement = $this->pdo->prepare("DELETE FROM folders where user_id = ?");
                $result = $statement->execute([$id]);

                # Delete all documents for the selected user
                $statement = $this->pdo->prepare("DELETE FROM documents where user_id = ?");
                $result = $statement->execute([$id]);

                # Delete user account
                $statement = $this->pdo->prepare("DELETE FROM users where user_id = ?");
                $result = $statement->execute([$id]);

                # All ok -> commit
                $this->pdo->commit();

    
            }
            catch(PDOException $e)
            {
                $this->logData('admin',3,'$e->getMessage()');
                return false;
                exit();
            }
            
            return true;

        }
        else
        {
            $this->logData('admin',1,"demande de suppression d'un compte depuis un compte non admin");
            return false;
        }
    }

}