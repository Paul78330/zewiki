<?php
require_once "../app/controllers/InputController.php";
require_once "../app/controllers/DisplayController.php";
require_once "../app/controllers/AbstractController.php";
require_once "../app/controllers/MailsController.php";
require_once "../app/models/DatabaseManager.php";
require_once "../app/models/entities/User.php";
require_once "../app/models/entities/Folder.php";
require_once "../app/models/entities/Document.php";

class UserController extends AbstractController{

##-------------------- Methods ------------------------------------------------------------------------------------------------------------------------------------------

    # Default user page (not connected user)
    public function welcome()
    {
        # Create empty var datas array
        $datas = array();

        # Get common tree from database (sql publictree view)
        $commonObjectsTree = $this->getObjectsTree('publictree');

        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_base.php';

        # Set tree in $_SESSION
        $_SESSION['tree'] = $commonObjectsTree;
        $_SESSION['source'] = 'common';

        # add vars to template and call template
        $this->render($datas);
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # User clicked on connection button to login (sf = show form)
    public function p1_sf_connection()
    {
        # Create empty var datas array
        $datas = array();

        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_connection.php';

        # add vars to template and call template
        $this->render($datas);
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # User clicked on disconnect button to logout
    public function p1_ex_disconnect()
    {
        # Reset datas stored in $_SESSION
        unset ($_SESSION['user']);

        # Call welcome page
        $this->welcome();
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # User clicked on create account button (sf = show form)
    public function p1_sf_addAccount()
    {
        # Create empty var datas array
        $datas = array();

        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_register.php';

        # add vars to template and call template
        $this->render($datas);
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # User clicked on connexion button from form connexion
    public function p5_ex_connection()
    {
        # Create DatabaseManager object to access database
        $databasemanager = new DatabaseManager();
        # Clean input from form
        $email =  InputController::cleanInput($_POST["email"]);
        $password =  InputController::cleanInput($_POST["password"]);

        # Verfify all fields exist from form
        if($email == false || $password == false){
            $_SESSION['alert']['message']= "champs vides";
            
            # Recall the form
            $this->redirectTo('p1-sf-connection');
        }

        # Verfify email from form
        if(InputController::valide_mail($email) == false){
            $_SESSION['alert']['message'] = "Email invalide";
            $this->redirectTo('p1-sf-connection');
        }

        # Verfify password from form
        if(InputController::valide_password($password) == false ){
            $_SESSION['alert']['message'] = "Mot de passe invalide";
            # Recall the form
            $this->redirectTo('p1-sf-connection');
        }

        # Verfiy account exists in Database
        $accountExists= $databasemanager->getUserByEmail($email);
        if($accountExists)
        {
            $_SESSION['alert']['message'] = "Adresse e-mail inconnue";
            # Recall the form
            $this->redirectTo('p1-sf-connection');
        }

        # Verify if account is activated
        
        $isActive = $databasemanager->is_active($email);
        
        if(!$isActive){
            $_SESSION['alert']['message'] = "Votre compte n'est pas activé";
            # Call Send activation code form
            $this->redirectTo('p5-sf-activeAccount');
        }

        # Fields ok account activated : verify password from database
        $validPassword = $databasemanager->verifyPassword($email,$password);
        if ($validPassword == false){
            # email or password wrong : back to connection
            $_SESSION['alert']['message'] = "Informations d'identifications incorrectes";
            # Recall the form
            $this->redirectTo('p1-sf-connection');
    }
    else {  
            # Authentication ok :

            # Store user datas in $_SESSION
            $databasemanager->getUserDatas($email);

            # Save lastConnection date
            $databasemanager->setLastConnectionDate($email);

            # Log connection
            $databasemanager->logData('user',1,"connexion utilisateur réussie");

            # Get home tree from database (sql hometree view)
            $commonObjectsTree = $this->getObjectsTree('hometree');

            # Set datas in $datas array
            $datas['p2_view'] = 'p2_base.php';
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_base.php';
    
            # Set tree in $_SESSION
            $_SESSION['tree'] = $commonObjectsTree;
    
            # add vars to template and call template
            $this->render($datas);
    }
}
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function p5_sf_resetPassword()
    {
        # Create empty var datas array
            $datas = array();
    
            # Set datas in $datas array
            $datas['p2_view'] = 'p2_base.php';
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_resetPassword.php';
    
            # add vars to template and call template
            $this->render($datas);
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function p5_ex_resetPassword()
{
    # Clean input from form
    $email =  InputController::cleanInput($_POST["email"]);

    # Verfify email field exists from form
    if($email == false)
    {
        $_SESSION['alert']['message']= "Adresse e-mail vide";
    }
    else
    {
        # Verify email in database
        $databasemanager = new DatabaseManager();
        $emailFound = $databasemanager->getUserByEmail($email);
        if($emailFound){
            # Create a secure token
            $token = bin2hex(random_bytes(32));

            $message = "cliquez sur le lien suivant : <a href='http://192.168.1.106/zewiki/resetPassword?token=" . urlencode($token) . "'>Réinitialiser mot de passe</a>";
            $subject = 'Réinitialisation de votre mot de passe';

            # Save token, email in database
            $result = $databasemanager->saveTokenInDatabase($email,$token,'reset_password');
            if($result)
            {
                $mailcontroller = new MailsController();
                $resultat = $mailcontroller->sendMail($email,$subject, $message);
                if($resultat)
                {
                    $_SESSION['alert']['message'] = "Un e-mail à été envoyé, cliquez sur le lien pour reinitialiser votre mot de passe. <br> Vérifiez vos spams si vous ne le trouvez pas, sinon recommencez. ";
                }
                else
                {
                    # Sending email failed :
                    $_SESSION['alert']['message'] = "Une erreur s'est produite lors de l'envoi de l'e-mail, veuillez réessayer ulterieurement. ";
                }
            }
            
            else
            {
                # Saving token failed :
                $_SESSION['alert']['message'] = "Une erreur s'est produite, veuillez réessayer ulterieurement. ";
            }    
        }
        else
        {   
            $_SESSION['alert']['message'] = "Adresse e-mail inconnue ";
        }
    
    }
    # Recall the form
    $this->redirectTo('p5-sf-resetPassword');
 
}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function resetPasswordSF(){
        # Get token from url
        $token = $_GET['token'];

        # Get email and date from database
        $dbmanager = new DatabaseManager();
        $infos = $dbmanager->getTokenInfos($token);
        $creationDate = new DateTime($infos['created_at']);

        # Add 5 mn to the creation date
        $validDate = $creationDate->add(new DateInterval('PT5M'));

        # Get currentDate 
        $now = new DateTime();

        # Verify if link is expired
        if($now > $validDate)
        {
            # link ko, show form to create new link
            $_SESSION['alert']['message'] = "Le lien est expiré, veuillez recommencer ";
            $this->redirectTo('p5-sf-resetPassword');
        }
        else
        {
            # link ok set token in $_SESSION
            $_SESSION['token'] = $token;

            # Set datas in $datas array
            $datas['p2_view'] = 'p2_base.php';
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_newPassword.php';

            # add vars to template and call template
            $this->render($datas);
        }

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Show form to active account 
    public function p5_sf_activeAccount(){

        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_activeAccount.php';

        # add vars to template and call template
        $this->render($datas);    

    }
    
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function p5_ex_activeAccount(){
        
        $databasemanager = new DatabaseManager;
        
        # Get token from url
        $token = $_GET['token'];

        # Active user from token
        $result = $databasemanager->activeAccountByToken($token);

        if($result)
        {
            # Redirect to welcome page for authentication
            $this->redirectTo('welcome');

        }
        else
        {
            # Account not activated show activeAccount
            # Set datas in $datas array
            $datas['p2_view'] = 'p2_base.php';
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_activeAccount.php';

        # add vars to template and call template
        $this->render($datas);    

        }
        
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Send a token by email 
    public function sendSecureToken(string $tokenSource,string $email,string $message, string $subject,string $token):bool{

        $databasemanager = new DatabaseManager;

        # Save the token in database
        $result = $databasemanager->saveTokenInDatabase($email,$token,$tokenSource);
        
        if($result){
            # send email 
            $mailcontroller = new MailsController;
            $result = $mailcontroller->sendMail($email,$subject, $message);
            if($result){
                # Mail sent
                return true;
            }
            else{
                # Error sending mail
                return false;
            }
        }
        else{
            # Error saving token in database
            return false;
        }
    }
    
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function newPassword()
    {

        # Clean input from form
        $password =  InputController::cleanInput($_POST["password"]);
        $password2 =  InputController::cleanInput($_POST["password2"]);
        
        # Verfify all fields exist from form
        if($password == false || $password2 == false)
        {
            $_SESSION['alert']['message']= "Mot de passe vide";
            $this->redirectTo('newPassword');
            
        }

        # Verfify all fields corrects from form
        if(InputController::valide_password($password) == false || InputController::confirm_password($password,$password2) == false){
            $_SESSION['alert']['message']= "Mot de passe invalide";
            $this->redirectTo('newPassword');
        }
        # Passwords ok change the password in database
        $databasemanager = new DatabaseManager;
        $result = $databasemanager->setNewPasswordFromToken($password);

        if ($result)
        {

            # Create toast message : 
            $toast = DisplayController::creerRedirectToast("Mot de passe","Le nouveau mot de passe à été modifié, vous allez être redirigé vers la page de connexion",'p1-sf-connection');


            # Set datas in $datas array
            $datas['p2_view'] = 'p2_base.php';
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_newPassword.php';
            $datas['toast']  = $toast;
 
            # add vars to template and call template
            $this->render($datas);
            
        }
        else
        {   
            # Saving new password ko
            $_SESSION['alert']['message']= "Echec de reinitialisation du mot de passe...";

            # Set datas in $datas array
            $datas['p2_view'] = 'p2_base.php';
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_newPassword.php';

            # add vars to template and call template
            $this->render($datas);
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function p1_ex_myAccount(){
        # Add vars for views
        $datas = array('title' => 'mon compte');

        # set content_choice to content_visitor
        $_SESSION['content_choice'] = '5_content_moncompte';

        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_myAccount.php';

        # add vars to template and call template
        $this->render($datas);

    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function view(){

        # Get selected documentId
        $documentId = $_GET['id'];

        # Get document content from database
        $dbmanager = new DatabaseManager;
        # Get document_content column from the sql result
        $content = ($dbmanager->getDocumentById($documentId))['document_content'];

        # Set content and ID in $_SESSION
        $_SESSION['lastDocument']['content']= $content;
        $_SESSION['lastDocument']['id']= $documentId;
        $_SESSION['selectedItem'] = 'document';

        # Add vars for views
        $data = array('title' => 'view ');

        # Set content_choice to 5_content_document
        $_SESSION['content_choice'] = '5_content_document';
        $_SESSION['actions_choice'] = '4_actions_home';

        # add views to viewlist
        $viewsListe = array('3_folders_liste.php', $_SESSION['actions_choice'] . ".php",$_SESSION['content_choice'].".php");
        $this->render($viewsListe,$data);
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function showRegister(){
        # Add vars for views
        $data = array("title" => "créer un compte");

        # Set content_choice to 5_content_inscription
        $_SESSION['content_choice'] = '5_content_inscription';

        # add views to viewlist
        $viewsListe = array('3_folders_liste.php','4_actions_liste.php',$_SESSION['content_choice'].".php");
        $this->render($viewsListe,$data);
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function p5_ex_register()
    {
        # Clean input from form
        $firstname =  InputController::cleanInput($_POST["firstname"]);
        $lastname =  InputController::cleanInput($_POST["lastname"]);
        $email =  InputController::cleanInput($_POST["email"]);
        $password =  InputController::cleanInput($_POST["password"]);
        $confirm_password =  InputController::cleanInput($_POST["confirm_password"]);
    
        # Verfify all fields exist from form
        if($firstname == false || $lastname == false || $email==false || $password == false || $confirm_password == false)
        {
            $_SESSION['alert']['message'] = 'Un des champs obligatoire est vide';
            $this->redirectTo("p1-sf-addAccount");
        }
        # Verfify all fields corrects from form
        if(InputController::valide_mail($email) == false || InputController::valide_password($password) == false || InputController::confirm_password($password,$confirm_password) == false)
        {
            $_SESSION['alert']['message'] = 'Un des champs est incorrect';
            $this->redirectTo("p1-sf-addAccount");
        }
      
        # Fields ok : create user from contructor with field from form
        $user = new User($firstname,$lastname,$email);
        # Add password
        $user->setPassword($password);
      
        # New user creation
        $databasemanager = new DatabaseManager();
        $resultat = $databasemanager->insertUser($user);

        if ($resultat == false){
                DisplayController::messageAlert("Une errreur s'est produite",DisplayController::ROUGE);
                die;
            }
            # User creation ok 
            else
            {
                # Create a secure token for account activation
                $token = bin2hex(random_bytes(32));
                
                # Message creation 
                $message = "cliquez sur le lien suivant : <a href='http://192.168.1.106/zewiki/p5-ex-activeAccount?token=" . urlencode($token) . "'>Activer votre compte</a>";
                $subject = 'Activation de votre compte Zewiki';
                # Save token in Database, and send email with the token
                $result = $this->sendSecureToken('activation_account',$email,$message,$subject,$token);

                if($result)
                {
                    # Create toast message : 
                    $toast = DisplayController::creerRedirectToast("Activation","Un message d'activation vous a été envoyé, cliquez dessus pour activer votre compte puis connectez-vous.",'welcome');

                    # Set datas in $datas array
                    $datas['p2_view'] = 'p2_base.php';
                    $datas['p3_view'] = 'p3_base.php';
                    $datas['p5_view'] = 'p5_register.php';
                    # show toast message and redirect to the welcome page (p5_register.php toast datas-)
                    $datas['toast']  = $toast;
                    
                    # add vars to template and call template
                    $this->render($datas);
                }
                else
                {
                    $_SESSION['alert']['message'] = "Une erreur s'est produite lors de l'envoi de l'e-mail, veuillez réessayer ulterieurement. ";
                    $this->redirectTo("p1-sf-addAccount");
                }
           }
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------   
    public function connexion(){
        # Add vars for views
        $data = array("title" => "se connecter");

        # set default content_choice to content_visitor
        $_SESSION['content_choice'] = '5_content_connexion';
        $_SESSION['actions_choice'] = '4_actions_liste';

        # add views to viewlist
        $viewsListe = array('3_folders_liste.php',$_SESSION['actions_choice'] . '.php',$_SESSION['content_choice'] . '.php');
        $this->render($viewsListe,$data);
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function erreur(){
        include VIEWS_PATH. 'error.php';
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # icon clicked in nav-bar
    public function nav()
    {
        # get nav choice from url

        switch($_GET['choice']){
            case 'common':
                $sql_view = 'publictree'; // database view name
                $treename = "common";
                $_SESSION['actions_choice']='4_actions_liste';
                break;

            case 'home':
                $sql_view = 'hometree'; // database view name
                $treename = "home";
                # Set actions_choice in $_SESSION
                $_SESSION['actions_choice']='4_actions_home';
                break;

            case 'shares':
                $sql_view = 'sharetree'; // database view name
                $treename = "shares";
                $_SESSION['actions_choice']='4_actions_liste';
                break;
        }

        # Set nav_choice in $_SESSION
        $_SESSION['nav_choice']= $_GET['choice'];

        # set requested tree in $_SESSION
        $this->getObjectsTree($sql_view,$treename);

        # Add vars for views
        $data = array("title" => "nav");

        # add views to viewlist
        $viewsListe = array('3_folders_liste.php',$_SESSION['actions_choice'] . '.php',$_SESSION['content_choice'] . '.php');
        $this->render($viewsListe,$data);
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function getObjectsTree(string $sql_view):array{

        # Get requested tree from database
        $dbmanager = new DatabaseManager;
        $items = $dbmanager->getTree($sql_view);

        # Convert view rows in objects array
        $ArrayItems =[];
        foreach ($items as $item){
        
            if($item['type'] == 'folder')
            {
                # Folder processing, create folder object 
                $currentFolder = new Folder($item['name'],$item['left_edge'],$item['right_edge']);   
                $currentFolder->setId($item['id']);
                $currentFolder->setStatus("open");
                # store currentFolder in ArrayItems
                array_push($ArrayItems,$currentFolder);
            }
            else
            {
                # document processing, create document object 
                $currrentDocument = new Document($item['name'],$item['left_edge'],$item['right_edge']);
                $currrentDocument->setId($item['id']);
                # store currentDocument in ArrayItems
                array_push($ArrayItems,$currrentDocument);
            }
        }

        # Return the objects array
        return $ArrayItems;
    }


  
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # remove all datas stored $_SESSION
    public function exit(){
        unset($_SESSION['user']);
        unset($_SESSION['common']);
        unset($_SESSION['alert']);
        unset($_SESSION['home']);
        unset($_SESSION['shares']);
        session_destroy();
        $this->redirectTo("welcome");
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public static function isSessionAdmin():bool{
        if(!isset($_SESSION['user'])){
            # User not connected
            return false;
        }
        $currentUser = $_SESSION['user'];
        if($currentUser->getIsadmin() == true){
            # User connected and user admin
            return true;
        }
        return false;
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function showNewFolderForm(){
        # Clic on new folder button :
        # Add vars to data
        $data = array('title' => 'new folder');

        # Add views to viewlist
            $viewsList = array('3_folders_liste.php',$_SESSION['actions_choice'] . '.php','5_content_new_folder.php');
            $this->render($viewsList,$data);
    }


# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function showNewDocumentForm(){
    # Click on new document button :
    # Add vars to data
     
    # Set datas in $datas array
    $datas['p2_view'] = 'p2_base.php';
    $datas['p3_view'] = 'p3_base.php';
    $datas['p4_view'] = 'p4_base.php';
    $datas['p5_view'] = '5_content_new_document.php';


    # add vars to template and call template
    $this->render($datas);

}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function showEditDocumentForm(){
    # Click on edit document button :
    # Add vars to data
    $data = array('title' => 'new document');
    
    # Set actions_choice
    $_SESSION['actions_choice'] = '4_actions_home';

    # add views to viewlist
        $viewsList = array('3_folders_liste.php',$_SESSION['actions_choice'] . '.php','5_content_document_edit.php');
        $this->render($viewsList,$data);
}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function toggleFolder():void
    {   
        # Get folder id and source from request    
        $id = $_GET['id'];
        $source = $_GET['source'];
        # Set last selected folder infos in $_SESSION
        $_SESSION['lastSelectedFolderId']=$id;
        $_SESSION['lastSelectedFolderSource']=$source;
        $_SESSION['selectedItem']='folder';
   

        $_SESSION['nav_choice']=$source;
        # loop on S_SESSION[$source] to find the good object and toggle status
        foreach (($_SESSION['tree']) as $item){
            if( $item->getId()==$id)
            # Item found
            {
                if ($item->getStatus() =='closed'){$item->setStatus('open');}
                else{$item->setStatus('closed');}
                break;
            }
        }

        # Set actions_choices
        $_SESSION['actions_choice']='4_actions_home';


        # Create empty var datas array
        $datas = array();

        # Get common tree from database (sql publictree view)
        $commonObjectsTree = $this->getObjectsTree('publictree');

        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = 'p5_base.php';


        # add vars to template and call template
        $this->render($datas);
    }


# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function addFolder()
    {

        # add vars to data
        $data = array('title' => 'success');
        
        # Clean input from form
        $folderName =  InputController::cleanInput($_POST["newFolderName"]);
        $parentId = $_POST["parentId"];
        $userId =  $_POST["userId"];

        # Verfify all fields exist from form
        if($folderName == false || $parentId == false){
            $this->redirectTo("showNewFolderForm");
        }

        # Verfify field correct from form
        if(InputController::valide_folder_name($folderName) == false){
            $this->redirectTo("showNewFolderForm");
        }

        # Add folder in Database :
        $dbmanager = new DatabaseManager();
        $result = $dbmanager->addItemByParentId($parentId, $folderName,'folder', $userId);


        if($result){
            # Set content_choice in $_SESSION
            $_SESSION['content_choice']='5_content_new_folder';
            $viewsList = array('3_folders_liste.php','4_actions_liste.php',$_SESSION['content_choice'] . '.php');
            $this->render($viewsList,$data);
        }
        else{
            $this->redirectTo("showNewFolderForm");
        }
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function addDocument()
{

   
    
    # Clean input from form
    $documentName =  InputController::cleanInput($_POST["newDocumentName"]);
    $parentId = $_POST["parentId"];
    $userId =  $_POST["userId"];
    $documentContent = $_POST ["documentContent"];

    # Verfify all fields exist from form
    if($documentName == false || $parentId == false){
        $this->redirectTo("show-newDocument-form");
    }

    # Verfify field correct from form
    if(InputController::valide_folder_name($documentName) == false){
        $this->redirectTo("show-newDocument-form");
    }

    # Add document in Database :
    $dbmanager = new DatabaseManager();
    $result = $dbmanager->addItemByParentId($parentId, $documentName, 'document', $userId,$documentContent);

    if($result){


        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = '5_content_new_document.php';


        # add vars to template and call template
        $this->render($datas);

    }
    else{
        $this->redirectTo("show-newDocument-form");
    }
}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function updateDocument()
{

    # add vars to data
    $data = array('title' => 'success');
    
    # Clean input from form
    $documentId = $_POST["documentId"];
    $documentContent = $_POST ["documentContent"];

    # Verfify all fields exist from form
    if($documentId == false || $documentContent == false){
        $this->redirectTo("show-editDocument-form");
    }

    # Update document in Database :
    $dbmanager = new DatabaseManager();
    $result = $dbmanager->updateDocumentById($documentId,$documentContent);

    # if update ok : update document in $_SESSION
    if($result)
    {
        $_SESSION['lastDocument']['content']= $documentContent;
        $_SESSION['lastDocument']['id']= $documentId;

        # Set content_choice and actions_choice in $_SESSION
        $_SESSION['content_choice']='5_content_document';
        $_SESSION['actions_choice'] = '4_actions_home';

        $viewsList = array('3_folders_liste.php','4_actions_liste.php',$_SESSION['content_choice'] . '.php');
        $this->render($viewsList,$data);
    }

    else{
        $this->redirectTo("show-editDocument-form");
    }
}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function deleteDocument()
    {

        # add vars to data
        $data = array('title' => 'success');
        
        # get datas from session
        $documentId =  $_SESSION['lastDocument']['id'];

        switch($_SESSION['lastSelectedFolderSource']){

            # click on login button
            case 'home' :
                $documentSource = $_SESSION['user']->getId();
                break;
            case 'common' :
                $documentSource = 2;
                break;

            case 'shares' :
                $documentSource = 3;
                break;
        }

        # Verfify all fields exist from form
        if($documentId == false) {
            $this->redirectTo("show-editDocument-form");
        }

        # Delete document in Database :
        $dbmanager = new DatabaseManager();
        $result = $dbmanager->deleteDocumentById($documentId,$documentSource);

        # if delete ok 
        if($result)
        {
            $_SESSION['lastDocument']['content']= '';
            $_SESSION['lastDocument']['id']= '';

         
            # Set content_choice and actions_choice in $_SESSION
           // $_SESSION['content_choice']='5_content_folder';
            //$_SESSION['actions_choice'] = '4_actions_home';

            //$viewsList = array('3_folders_liste.php','4_actions_liste.php',$_SESSION['content_choice'] . '.php');
            //$this->render($viewsList,$data);
            if(isset($_SESSION['lastSelectedFolderSource'])){
                $choice = $_SESSION['lastSelectedFolderSource'];
            }
            else {
                $choice = 'common';
            }
            #$this->redirectToRoute('nav?choice='. $choice);
        }

        else{
            $this->redirectTo("show-editDocument-form");
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function deleteFolder()
    {

        # add vars to data
        $data = array('title' => 'success');
        
        # get datas from session
        $folderId =  $_SESSION['lastSelectedFolderId'];

        switch($_SESSION['lastSelectedFolderSource']){

            # click on login button
            case 'home' :
                $folderSource = $_SESSION['user']->getId();
                break;
            case 'common' :
                $folderSource = 2;
                break;

            case 'shares' :
                $folderSource = 3;
                break;
        }

        # Verfify all fields exist from form
        if($folderId == false) {
            $this->redirectTo("show-editDocument-form");
        }

  
        # Delete document in Database :
        $dbmanager = new DatabaseManager();
        $result = $dbmanager->deleteFolderById($folderId,$folderSource);

        # if delete ok 
        if($result)
        {
            $_SESSION['lastSelectedFolderId'] = '';

         
            # Set content_choice and actions_choice in $_SESSION
           // $_SESSION['content_choice']='5_content_folder';
            //$_SESSION['actions_choice'] = '4_actions_home';

            //$viewsList = array('3_folders_liste.php','4_actions_liste.php',$_SESSION['content_choice'] . '.php');
            //$this->render($viewsList,$data);
            if(isset($_SESSION['lastSelectedFolderSource'])){
                $choice = $_SESSION['lastSelectedFolderSource'];
            }
            else {
                $choice = 'common';
            }
            #$this->redirectToRoute('nav?choice='. $choice);
        }

        else{
            $this->redirectTo("show-editDocument-form");
        }
    }


    

   
}
