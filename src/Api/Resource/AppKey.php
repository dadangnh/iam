<?php


namespace App\Api\Resource;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class AppKey
 * @ApiResource(
 *      itemOperations={
 *          "get",
 *          "post_auth" ={
 *              "method"="POST",
 *              "path"="/app_keys",
 *              "controller"=AppKeyController::class,
 *          }
 *      }
 * )
 */
final class AppKey
{
    /**
     * @ApiProperty(identifier=true)
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $plain_password;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plain_password;
    }

    /**
     * @param string $plain_password
     */
    public function setPlainPassword(string $plain_password): void
    {
        $this->plain_password = $plain_password;
    }
}