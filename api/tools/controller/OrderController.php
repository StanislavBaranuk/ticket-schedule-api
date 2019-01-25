<?php

class OrderController extends Controller {

    function __construct()
    {
        $this->model = $this->getModel(__CLASS__);
    }

    function addAction($params = []) {
        Access::_RUN_(["authorization"]);
        $this->model->add(Parser::json());
    }

    function deleteAction($params = []) {
        Access::_RUN_(["authorization", "admin"]);
        $this->model->delete(Parser::json());
    }

    function getAction () {
        Access::_RUN_(["authorization"]);
        $this->model->get(Parser::json());
    }

    function getByIdAction () {
        Access::_RUN_(["authorization"]);
        $this->model->getById(Parser::json());
    }

}