<?php

namespace FreshMail\Interfaces;

interface TransactionEmail
{

    public function getTemplate();
    public function getSubject($idLang);
    public function getContent($idLang);
}