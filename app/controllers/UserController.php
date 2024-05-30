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
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_base.php';

        # Set tree in $_SESSION
        $_SESSION['tree'] = $commonObjectsTree;
        $_SESSION['source'] = 'common';

        # Set the content to show 
        $_SESSION['last_p5_view'] = 'p5_base.php';

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
        if(!$accountExists)
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

            $message = "cliquez sur le lien suivant : <a href='https://zewiki.fr/resetPassword?token=" . urlencode($token) . "'>Réinitialiser mot de passe</a>";
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
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_activeAccount.php';

        # add vars to template and call template
        $this->render($datas);    
    }
    
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function p5_ex_activeAccount()
    {
       
        $databasemanager = new DatabaseManager;

        # case function called from url with token 
        # Verify token exists (page from link) or not page from connexion with non activated account

        if(isset($_GET['token']))
        {
            # Get token from url
            $token = $_GET['token'];

            # Active user from token
            $activation = $databasemanager->activeAccountByToken($token);

            if($activation)
            {
                # Redirect to welcome page for authentication
                $this->redirectTo('welcome');
            }
        }

        # Case function call from form 
      
        # Clean input from form
        $email =  InputController::cleanInput($_POST["email"]);

        # Verfify email field exists from form
        if($email == false)
        {
            $_SESSION['alert']['message']= "Adresse e-mail vide";
        }

        # Verfiy account exists in Database
        $accountExists= $databasemanager->getUserByEmail($email);
        if(!$accountExists)
        {
            $_SESSION['alert']['message'] = "Adresse e-mail inconnue";
            # Recall the form
            $this->redirectTo('p5-ex-activeAccount');
        }

        # Send e-mail to activeAccount

        # Create a secure token for account activation
        $token = bin2hex(random_bytes(32));

        # Message creation 
        $message = "cliquez sur le lien suivant : <a href='https://zewiki.fr/p5-ex-activeAccount?token=" . urlencode($token) . "'>Activer votre compte</a>";
        $subject = 'Activation de votre compte Zewiki';
        # Save token in Database, and send email with the token
        $envoi = $this->sendSecureToken('activation_account',$email,$message,$subject,$token);

        if($envoi)
        {
            # Create toast message : 
            $toast = DisplayController::creerRedirectToast("Activation","Un message d'activation vous a été envoyé, cliquez dessus pour activer votre compte puis connectez-vous.",'https://www.zewiki.fr');

            # Set datas in $datas array
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_activeAccount.php';
            $datas['toast']  = $toast;
            
            # add vars to template and call template
            $this->render($datas);
        }
        else
        {
            $_SESSION['alert']['message'] = "Une erreur s'est produite lors de l'envoi de l'e-mail, veuillez réessayer ulterieurement. ";
            $this->redirectTo("p1-sf-connection");
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
            $datas['p3_view'] = 'p3_base.php';
            $datas['p5_view'] = 'p5_newPassword.php';

            # add vars to template and call template
            $this->render($datas);
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function p1_ex_myAccount()
    {
        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_myAccount.php';

        # Set the content to show 
        $_SESSION['last_p5_view'] = 'p5_myAccount.php';

        # add vars to template and call template
        $this->render($datas);
    }
# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Display the selected document in content part
    public function view($view){

        # Get selected documentId
        # if not exist 'id' get it from $_SESSION else store it in $_SESSION
        if(isset($_GET['id']))
        {
            $documentId = $_GET['id'];
            # Set $documentId in $_SESSION for edit view
            $_SESSION['lastSelectedDocumentId'] = $documentId;
        }
        else
        {
            $documentId = $_SESSION['lastSelectedDocumentId'];
        }

        # Get document content from database
        $dbmanager = new DatabaseManager;

        # Get document_content column from the sql result
        $content = ($dbmanager->getDocumentContentById($documentId))['document_content'];

        # Set content and ID in $_SESSION
        //$_SESSION['lastSelectedDocumentContent'] = $content;
        $_SESSION['lastSelectedDocumentId'] = $documentId;
        $_SESSION['selectedItem'] = 'document';

        # Set the last content view in $_SESSIN
        $_SESSION['last_p5_view'] = '5_content_document.php';

        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = $view;
        $datas['lastSelectedDocumentContent'] = $content;
        $datas['lastSelectedDocumentId'] = $documentId;
   
        # add vars to template and call template
        $this->render($datas);

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
                $message = "cliquez sur le lien suivant : <a href='https://zewiki.fr/p5-ex-activeAccount?token=" . urlencode($token) . "'>Activer votre compte</a>";
                $subject = 'Activation de votre compte Zewiki';
                # Save token in Database, and send email with the token
                $result = $this->sendSecureToken('activation_account',$email,$message,$subject,$token);

                if($result)
                {
                    # Create toast message : 
                    $toast = DisplayController::creerRedirectToast("Activation","Un message d'activation vous a été envoyé, cliquez dessus pour activer votre compte puis connectez-vous.",'welcome');

                    # Set datas in $datas array
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
        # get nav choice from url (resfresh after delete )
        if (isset($_GET['choice']))
        {
            $choice = $_GET['choice'];
        }
        else
        {
            $choice = $_SESSION['nav_choice'];
        }

        switch($choice){
            case 'common':
                $sql_view = 'publictree'; // database view name
                break;

            case 'home':
                $sql_view = 'hometree'; // database view name
                break;

            case 'shares':
                $sql_view = 'sharetree'; // database view name
                break;

            case 'admin':
                $sql_view = 'hometree'; // database view name
                $this->show_admin_panel();
                exit();
                break;
        }

        # Set nav_choice in $_SESSION
        $_SESSION['nav_choice'] = $choice;

        # Get requested tree from Database
        $selectedTree = $this->getObjectsTree($sql_view);

        # Do not modify the content so show last content
        $contentToShow = $_SESSION['last_p5_view'];

        # Set tree in $_SESSION
        $_SESSION['tree'] = $selectedTree;
        $_SESSION['source'] = $choice;

        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = $contentToShow ;
        
        
        # add vars to template and call template
        $this->render($datas);
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
        unset($_SESSION['token']);
        unset($_SESSION['content_choice']);
        unset($_SESSION['alert']);
        unset($_SESSION['tree']);
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

        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = '5_content_new_folder.php';

        # add vars to template and call template
        $this->render($datas);
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function showEditFolderForm(){
        # Clic on new folder button :

        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = 'p5_editFolder.php';

        # add vars to template and call template
        $this->render($datas);
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function showNewDocumentForm(){
    # Click on new document button :
     
    # Set datas in $datas array
    $datas['p3_view'] = 'p3_base.php';
    $datas['p4_view'] = 'p4_base.php';
    $datas['p5_view'] = '5_content_new_document.php';

    # add vars to template and call template
    $this->render($datas);

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

        # Create empty var datas array
        $datas = array();

        # Get common tree from database (sql publictree view)
        $commonObjectsTree = $this->getObjectsTree('publictree');

        # Do not modify the content so show last content
        $contentToShow = $_SESSION['last_p5_view'];

        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = $contentToShow;

        # add vars to template and call template
        $this->render($datas);
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function addFolder()
    {
        # Clean input from form
        $folderName =  InputController::cleanInput($_POST["newFolderName"]);
        $parentId = $_POST["parentFolderId"];
        $userId =  $_POST["userId"];

        # Verfify all fields exist from form
        if($folderName == false || $parentId == false){
            $this->redirectTo("show-newfolder-form");
        }

        # Verfify field correct from form
        if(InputController::valide_folder_name($folderName) == false){
            $this->redirectTo("show-newfolder-form");
        }

        # Add folder in Database :
        $dbmanager = new DatabaseManager();
        $result = $dbmanager->addItemByParentId($parentId, $folderName,'folder', $userId);

        # Folder added in database
        if($result)
        {
            # Set message, refresh tree nav function with last $_SESSION['nav_choice'], and show the message in p5_message.php
            $_SESSION['alert']['message'] = "Le dossier a été crée";
            $_SESSION['last_p5_view'] = 'p5_message.php';
            $this->nav();
        }
        else{
            $this->redirectTo("show-newfolder-form");
        }
    }


# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function renameFolder()
    {
        # Clean input from form
        $folderName =  InputController::cleanInput($_POST["newFolderName"]);
        $folderId = $_POST["folderId"];

        # Verfify all fields exist from form
        if($folderName == false){
            $this->redirectTo("show-editfolder-form");
        }

        # Verfify field correct from form
        if(InputController::valide_folder_name($folderName) == false){
            $this->redirectTo("show-editfolder-form");
        }

        # fields ok rename folder in database
        $dbmanager = new DatabaseManager();
        $renaming = $dbmanager->renameFolder($folderId, $folderName);

        # Rename folder ok
        if ($renaming)
        {
            # Set message, refresh tree nav function with last $_SESSION['nav_choice'], and show the message in p5_message.php
            $_SESSION['alert']['message'] = "Votre dossier a été renommé";
            $_SESSION['last_p5_view'] = 'p5_message.php';
            $this->nav();
        }
        else
        # Error renaming folder return to form
        {
            $this->redirectTo("show-editfolder-form");
        }

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function addDocument()
{

    # Clean input from form
    $documentName =  InputController::cleanInput($_POST["newDocumentName"]);
    $parentId = $_POST["parentId"];
    $userId =  $_POST["userId"];

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
    $result = $dbmanager->addItemByParentId($parentId, $documentName, 'document', $userId);

    if($result){

        # Set message, refresh tree nav function with last $_SESSION['nav_choice'], and show the message in p5_message.php
        $_SESSION['alert']['message'] = "Le document a été ajouté";
        $_SESSION['last_p5_view'] = 'p5_message.php';
        $this->nav();

    }
    else{
        $this->redirectTo("show-newDocument-form");
    }
}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
public function updateDocument()
{
    
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
        $_SESSION['lastDocumentcontent']= $documentContent;
        $_SESSION['lastDocumentId']= $documentId;

        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = '5_content_document.php';

        # add vars to template and call template
        $this->render($datas);
    }

    else{
        $this->redirectTo("show-editDocument-form");
    }
}

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
   # Delete the last selected document
    public function deleteDocument()
    {

       # get $documentId and $documentSource from session
        $documentId =  $_SESSION['lastSelectedDocumentId'];
        $documentSource = $_SESSION['lastSelectedFolderSource'];
 
        # Delete document in Database $documentSource needed to update right and left edge from the good tree
        $dbmanager = new DatabaseManager();
        $result = $dbmanager->deleteDocumentById($documentId,$documentSource);

        # if delete ok 
        if($result)
        {
            unset($_SESSION['lastDocumentContent']) ;
            unset($_SESSION['lastDocumentId']);

            # Set message, refresh tree nav function with last $_SESSION['nav_choice'], and show the message in p5_message.php
            $_SESSION['alert']['message'] = "Votre document a été supprimé";
            $_SESSION['last_p5_view'] = 'p5_message.php';
            $this->nav();

        }

        else{
            $this->redirectTo("show-editDocument-form");
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function deleteFolder()
    {
        
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
            unset($_SESSION['lastSelectedFolderId']);
            # Set message, refresh tree nav function with last $_SESSION['nav_choice'], and show the message in p5_message.php
            $_SESSION['alert']['message'] = "Le dossier a été supprimé";
            $_SESSION['last_p5_view'] = 'p5_message.php';
            $this->nav();

            # Set datas in $datas array
            $datas['p3_view'] = 'p3_base.php';
            $datas['p4_view'] = 'p4_base.php';
            $datas['p5_view'] = 'p5_message.php';

            # add vars to template and call template
            $this->render($datas);
        }

        else{
            # Set message, refresh tree nav function with last $_SESSION['nav_choice'], and show the message in p5_message.php
            $_SESSION['alert']['message'] = "Le dossier n'a pas été supprimé, on ne peut pas supprimer un dossier non vide";
            $_SESSION['last_p5_view'] = 'p5_message.php';
            $this->nav();
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------

   # clique on 'administration' button from nav-bar
    public function show_admin_panel()
    {
        $databasemanager = new DatabaseManager;

        # get datas from database (users, logs and tokens)
        $users = $databasemanager->getUsersList();
        $logs = $databasemanager->getLogsList();
        $tokens = $databasemanager->getTokensList();

        
        # Set datas in $datas array
        $datas['p3_view'] = 'p3_base.php';
        $datas['p4_view'] = 'p4_base.php';
        $datas['p5_view'] = '5_content_admin.php';
        $datas['users'] = $users;
        $datas['logs'] = $logs;
        $datas['tokens'] = $tokens;

        # add vars to template and call template
        $this->render($datas);

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Enable user from admin panel
    public function user_enable()
    {
        # Get token from url
        $id = $_GET['id'];

        $databasemanager = new DatabaseManager;
        $enable = $databasemanager->user_enable($id);
        if($enable)
        {
            $this->show_admin_panel();
        }

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Disable user from admin panel-------
    public function user_disable()
    {
        # Get token from url
        $id = $_GET['id'];

        $databasemanager = new DatabaseManager;
        $disable = $databasemanager->user_disable($id);
        if($disable)
        {
            $this->show_admin_panel();
        }
    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Delete user from admin panel
    public function user_delete()
    {
        # Get token from url
        $id = $_GET['id'];

        $databasemanager = new DatabaseManager;
        $databasemanager->user_delete($id);

        $databasemanager = new DatabaseManager;
        $delete = $databasemanager->user_delete($id);
        
        if($delete)
        {
            $this->show_admin_panel();
        }


    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # User upgrade to admin from admin panel
    public function user_upgrade()
    {
        # Get token from url
        $id = $_GET['id'];

        $databasemanager = new DatabaseManager;
        $upgrade = $databasemanager->user_upgrade($id);
        
        if($upgrade)
        {
            $this->show_admin_panel();
        }

    }

# -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    # Admin downgrade to user from admin panel
    public function user_downgrade()
    {
        # Get token from url
        $id = $_GET['id'];

        $databasemanager = new DatabaseManager;
        $downgrade = $databasemanager->user_downgrade($id);

        if($downgrade)
        {
            $this->show_admin_panel();
        }

    }


}
