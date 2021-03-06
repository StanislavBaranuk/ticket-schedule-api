<?php
/**
 * Created by PhpStorm.
 * User: stanislaw
 * Date: 1/15/19
 * Time: 00:32
 */

class UserController extends Controller
{
    private $user_data;
    private static $is_online = 0;

    function __construct()
    {
        $this->model = $this->getModel(__CLASS__);
        $this->user_data = Parser::json();
    }

    function getInfoAction  () {
        Access::_RUN_(["authorization"]);
        ResponseControl::outputGet($this->model->getInfo());
    }

    function loginAction () {
        ResponseControl::generateStatus(200, "");
        ResponseControl::outputGet($this->model->authorization($this->user_data['login'],$this->user_data['password']), ["code" => 401, 'message' => ""]);
    }

    function isAuthAction () {
        Access::_RUN_(["authorization"]);
        ResponseControl::outputGet(1);
    }

    function isAdminAction () {
        Access::_RUN_(["admin"]);
        ResponseControl::outputGet(1);
    }

    function logoutAction () {
        Access::_RUN_(["authorization"]);
        $this->model->logout();
    }

    function changePasswordAction () {
        Access::_RUN_(["authorization"]);
        $this->model->setPassword($this->user_data['old'], $this->user_data['new']);
    }

    function getMenuAction () {
        ResponseControl::outputGet($this->model->getMenu());
    }

    /**
     * Will send email with link for set a new password to the user
     *
     * @param POST-request with data of JSON type { "email" : <value> }
     */
    public function forgetPasswordSendEmailAction () {
        if (!empty($this->model->getPassword($this->user_data["email"]))) {
            $token = TokenGenerator::generate();
            $this->model->sendEmailWithTemporaryToken($this->user_data["email"], $token);
            $this->model->addTemporaryToken($this->user_data["email"], $token, "password");
            ResponseControl::outputGet('');
            return '';
        } else {
            ResponseControl::generateStatus(404, "User not found");
            return '';
        }
    }

    /**
     * The window for password changing by the link from email that was sent by forgetPasswordSendEmailAction
     *
     */
    public function forgetPasswordSetNewAction($token) {
        $email = $this->model->getTemporaryTokenUser($token);
        if (!empty($email) && !empty($_POST["password"])) {
            if (preg_match("/.(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $_POST["password"])) {
                $this->model->setFPassword($email, $_POST['password']);
                $this->model->deleteTemporaryToken($token);
                header("Location: ".FRONTEND_LINK);
            } else {
                $_POST['error'] = "Password wrong";
            }
        } else if (!empty($email)) {
            Component::show("newPassword");
        } else {
            echo "
            <div style='position: fixed; top: -15px; height: 100vh; width: 100vw; background-color: whitesmoke; padding: 20px; font-size: 20px'>
                <p>Link <a href='http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."'>".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."</a> unavailable already</p>
                <a href='https://ticket-schedule.herokuapp.com'>Go to home page</a>
            </div>
            ";
        }
    }

    public function registerAction() {
        $this->model->register($this->user_data);
    }

    public function setOnlineAction() {
        static::$is_online = 1;
    }

    public function onlineAction ($params) {
        Access::_RUN_(['authorization']);

        $this->model->online($params);

        ResponseControl::outputGet(["state" => $params]);
    }

    public function columnsAction ($params) {
        Access::_RUN_(['authorization', 'admin']);
        ResponseControl::outputGet($this->model->getTableColumns($params));
    }

    public function changeInfoAction () {
        Access::_RUN_(['authorization']);
        ResponseControl::outputGet($this->model->changeInfo(Parser::json()));
    }
}