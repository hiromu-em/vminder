<?php
declare(strict_types=1);

namespace Vminder;

use Vminder\Result;

class FormValidation
{
    /**
     * メールアドレス形式を検証する。
     * @param string|null $email
     * @return Result 検証結果
     */
    public function validateEmailFormat(?string $email): Result
    {
        if (empty($email)) {
            return Result::failure('メールアドレスを入力してください。');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Result::failure('メールアドレスの形式が間違っています。');

        } elseif (!checkdnsrr(substr(strrchr($email, "@"), 1), "MX")) {
            return Result::failure('メールアドレスの形式が間違っています。');

        }

        return Result::success();
    }

    /**
     * パスワード形式を検証する。
     * @param string|null $password
     */
    public function validatePasswordFormat(?string $password): Result
    {
        $errorMessages = [];

        if (empty($password)) {
            return Result::failure(['パスワードを入力してください']);
        }

        if (mb_strlen($password) < 8) {
            $errorMessages[] = "パスワードは8文字以上で入力してください。";
        }

        if (!preg_match('/[A-Za-z]/', $password)) {
            $errorMessages[] = "英字を1文字含めてください。";
        }

        if (!preg_match('/\d/', $password)) {
            $errorMessages[] = "数字を1文字含めてください。";
        }

        if (!preg_match('/[@#\$%\^&\*]/', $password)) {
            $errorMessages[] = "記号(@ # $ % ^ & *) を1文字含めてください。";
        }

        if (!empty($errorMessages)) {
            return Result::failure($errorMessages);
        }

        return Result::success();
    }
}