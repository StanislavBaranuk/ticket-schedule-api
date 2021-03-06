<?php

class OrderController extends Controller {

    private $request;
    private $code;

    function __construct()
    {
        $this->model = $this->getModel(__CLASS__);
        $this->request = Parser::json();
    }

    function addAction() {
        Access::_RUN_(["authorization"]);
        $this->request["CODE"] = TokenGenerator::generate();
        $this->request["QR_LINK"] = 'https://api.qrserver.com/v1/create-qr-code/?size=230x230&data=' . USE_LINK . $this->request["CODE"];
        $this->model->add($this->request);
        ResponseControl::outputGet('');
    }

    function deleteAction($params = []) {
        Access::_RUN_(["authorization", "admin"]);
        $this->model->delete($this->request);
    }

    function getAction () {
        Access::_RUN_(["authorization", "admin"]);
        ResponseControl::outputGet($this->model->get(Parser::json()));
    }

    function getByCodeAction () {
        Access::_RUN_(["authorization"]);
        ResponseControl::outputGet($this->model->getByCode($this->request));
    }

    function getByUserAction () {
        Access::_RUN_(["authorization"]);
        ResponseControl::outputGet($this->model->getByUser());
    }

    function cancelAction () {
        Access::_RUN_(["authorization"]);
        ResponseControl::outputGet($this->model->cancel(Parser::json()));
    }

    function countAction () {
        Access::_RUN_(["authorization", 'admin']);
        ResponseControl::outputGet($this->model->count(Parser::json()));
    }

    function profitAction () {
        Access::_RUN_(["authorization", 'admin']);
        ResponseControl::outputGet($this->model->profit(Parser::json()));
    }

    function useAction($code) {

        if ($code != '')  {
            $_POST["code"] = $code;
        }

        if (!empty($_POST["key"]) && !empty($_POST["code"])) {
            $_POST['error'] = $this->model->checkout($_POST["code"], $_POST["key"]);
        }

        Component::show("checkoutOrder");
    }
}