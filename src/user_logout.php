<?php
unset($_SESSION);
session_destroy();
header('Location:?do=user_login');