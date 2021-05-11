<?php


namespace App\Controller;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Core\Permission;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\utils\AplikasiUtils;
use App\utils\RoleUtils;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/', name: 'app_index')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('api_entrypoint', [], 301);
    }

    /**
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_roles_by_jabatan_pegawai', methods: ['POST'])]
    public function getRoleByJabatanPegawai(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $idJabatan = $content['id_jabatan_pegawai'];

        /** @var JabatanPegawai $jabatanPegawai */
        $jabatanPegawai = $this->entityManager
            ->getRepository(JabatanPegawai::class)
            ->findOneBy(['id' => $idJabatan]);

        if (null === $jabatanPegawai) {
            return $this->json(['error' => 'No jabatan record found']);
        }

        $roles = RoleUtils::getRolesFromJabatanPegawai($jabatanPegawai);

        if (empty($roles)) {
            return $this->json(['warning' => 'No roles associated with this Jabatan Pegawai']);
        }

        return $this->json([
            'roles_count' => count($roles),
            'roles' => RoleUtils::createRoleDefaultResponseFromArrayOfRoles($roles, $iriConverter)
        ]);
    }

    /**
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/get_aplikasi_by_token', methods: ['POST'])]
    public function getAplikasiByToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $listOfPlainRoles = $this->getUser()->getRoles();
        $listAplikasi = $listRoles = [];
        foreach ($listOfPlainRoles as $plainRole) {
            $role = $this->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['nama' => $plainRole]);
            if (null !== $role) {
                $listRoles[] = $role;
            }
        }

        foreach (RoleUtils::getAplikasiByArrayOfRoles($listRoles) as $aplikasi) {
            $listAplikasi[] = AplikasiUtils::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_aplikasi_by_role_name', methods: ['POST'])]
    public function getAplikasiByRoleName(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json(['warning' => 'No roles associated with this name']);
        }

        $listAplikasi = [];
        foreach (RoleUtils::getAplikasiByRole($role) as $aplikasi) {
            $listAplikasi[] = AplikasiUtils::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * This method provide all aplikasis by token, including unreleased one
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/get_all_aplikasi_by_token', methods: ['POST'])]
    public function getAllAplikasiByToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $listOfPlainRoles = $this->getUser()->getRoles();
        $listAplikasi = $listRoles = [];
        foreach ($listOfPlainRoles as $plainRole) {
            $role = $this->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['nama' => $plainRole]);
            if (null !== $role) {
                $listRoles[] = $role;
            }
        }

        foreach (RoleUtils::getAllAplikasiByArrayOfRoles($listRoles) as $aplikasi) {
            $listAplikasi[] = AplikasiUtils::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * This method provide all aplikasis by role_name, including unreleased one
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_all_aplikasi_by_role_name', methods: ['POST'])]
    public function getAllAplikasiByRoleName(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json(['warning' => 'No roles associated with this name']);
        }

        $listAplikasi = [];
        foreach (RoleUtils::getAllAplikasiByRole($role) as $aplikasi) {
            $listAplikasi[] = AplikasiUtils::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/get_permissions_by_token', methods: ['POST'])]
    public function getPermissionsByToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $listOfPlainRoles = $this->getUser()->getRoles();
        $uniquePermissions = $listPermissionsOnRoles = $listRoles = [];
        $uniquePermissionsCount = 0;
        foreach ($listOfPlainRoles as $plainRole) {
            $role = $this->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['nama' => $plainRole]);
            if (null !== $role) {
                $listRoles[] = $role;
            }
        }

        foreach ($listRoles as $role) {
            $permissions = $role->getPermissions();
            if (null !== $permissions) {
                /** @var Permission $permission */
                foreach ($permissions as $permission) {
                    $iri = $iriConverter->getIriFromItem($permission);
                    if (!in_array($iri, $uniquePermissions, true)) {
                        $uniquePermissionsCount++;
                        $uniquePermissions[] = $iri;
                    }
                }
                $listPermissionsOnRoles[] = [
                    $role->getNama() => $permissions
                ];
            }
        }

        return $this->json([
            'unique_permissions_count' => $uniquePermissionsCount,
            'unique_permissions' => $uniquePermissions,
            'list_per_role' => $listPermissionsOnRoles
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_permissions_by_role_name', methods: ['POST'])]
    public function getPermissionsByRoleName(Request $request): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json(['warning' => 'No roles associated with this name']);
        }

        $permissions = $role->getPermissions();

        return $this->json([
            'permissions_count' => count($permissions),
            'permissions' => $permissions
        ]);
    }

    /**
     * @param Request $request
     * @return Role|null
     * @throws JsonException
     */
    private function readRoleFromRoleNameRequest(Request $request): Role|null
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $roleName = $content['role_name'];

        return $this->getDoctrine()
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);
    }
}
