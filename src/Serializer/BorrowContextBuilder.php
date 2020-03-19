<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Borrow;

final class BorrowContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

//        if ($resourceClass === Borrow::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization) {
//            $context['groups'][] = 'put_role_admin';
//        }
        if ($resourceClass === Borrow::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_ADMIN') && false === $normalization && $request->getMethod() === 'PUT') {
            $context['groups'][] = 'put_role_admin';
        }
        return $context;
    }
}