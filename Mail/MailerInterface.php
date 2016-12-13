<?php

namespace LAG\DatabaseBundle\Mail;

interface MailerInterface
{
    public function sendBackupSuccessMail($attachment);
    public function sendBackupErrorMail($error);
}
