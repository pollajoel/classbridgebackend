<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security\Authenticator;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class KeycloakAuthenticator extends AbstractAuthenticator
{


    public function __construct(private readonly UserRepository $userRepository,
    private ParameterBagInterface $params,
    private JWTTokenManagerInterface $jwtManager
    )
    {}
    /**
     * @param string $token
     * @return string
     */
    protected function formatToken(string $token): string
    {
        return trim(preg_replace('/^(?:\s+)?[B-b]earer\s/', '', $token));
    }
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        // "auth-token" is an example of a custom, non-standard HTTP header used in this application
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('Authorization');
        if (null === $token || empty($token)) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('Token is not present in the request headers');
        }

        try {
            // Décodez le jeton JWT
            $decodeToken = $this->formatToken($token); 
            $data = $this->jwtManager-> parse($decodeToken);
        } catch (\Symfony\Component\Security\Core\Exception\AuthenticationException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $roles = isset($data['realm_access']['roles']) ? $data['realm_access']['roles'] : [];
        $username = $data['preferred_username'];
        $email = $data['email'];
        return new SelfValidatingPassport(new UserBadge($username, null, ["roles" => $roles, "email" => $email]));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }


    
}