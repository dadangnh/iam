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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $idJabatan = $content['id_jabatan_pegawai'];

        /** @var JabatanPegawai $jabatanPegawai */
        $jabatanPegawai = $this->entityManager
            ->getRepository(JabatanPegawai::class)
            ->findOneBy(['id' => $idJabatan]);

        if (null === $jabatanPegawai) {
            return $this->json([
                'code' => 404,
                'error' => 'No jabatan record found'
            ], 404);
        }

        $roles = RoleUtils::getRolesFromJabatanPegawai($jabatanPegawai);

        if (empty($roles)) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this Jabatan Pegawai'
            ], 404);
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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
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
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/roles/mapping', methods: ['POST'])]
    public function getMappingByRole(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $keyData = strtolower($content['key_data']);

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
        }

        // Make sure that only valid parameter allowed
        if (!in_array(
            $keyData,
            ['user', 'jabatan', 'unit', 'kantor', 'eselon', 'jenis_kantor', 'group'],
            true)
        ) {
            return $this->json([
                'code' => 404,
                'error' => 'Invalid key_data parameter'
            ], 404);
        }

        $response = array();
        if ('user' === $keyData) {
            $response['user_count'] = $role->getUsers()->count();
            $response['user'] = $role->getUsers();
        }

        if ('jabatan' === $keyData) {
            $response['jabatan_count'] = $role->getJabatans()->count();
            $response['jabatan'] = $role->getJabatans();
        }

        if ('unit' === $keyData) {
            $response['unit_count'] = $role->getUnits()->count();
            $response['unit'] = $role->getUnits();
        }

        if ('kantor' === $keyData) {
            $response['kantor_count'] = $role->getKantors()->count();
            $response['kantor'] = $role->getKantors();
        }

        if ('eselon' === $keyData) {
            $response['eselon_count'] = $role->getEselons()->count();
            $response['eselon'] = $role->getEselons();
        }

        if ('jenis_kantor' === $keyData) {
            $response['jenis_kantor_count'] = $role->getJenisKantors()->count();
            $response['jenis_kantor'] = $role->getJenisKantors();
        }

        if ('group' === $keyData) {
            $response['group_count'] = $role->getGroups()->count();
            $response['group'] = $role->getGroups();
        }

        return $this->json([
            $response
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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
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
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
        }

        $permissions = $role->getPermissions();

        return $this->json([
            'permissions_count' => count($permissions),
            'permissions' => $permissions
        ]);
    }

    /**
     * @param string $roleName
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/roles/{roleName}/aplikasis', methods: ['GET'])]
    public function showAplikasisFromRoleName(string $roleName, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $role = $this->getDoctrine()
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
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
     * @param string $roleName
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/roles/{roleName}/all_aplikasis', methods: ['GET'])]
    public function showAllAplikasisFromRoleName(string $roleName, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $role = $this->getDoctrine()
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
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
     * @param string $roleName
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/roles/{roleName}/permissions', methods: ['GET'])]
    public function showPermissionsByRoleName(string $roleName, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $role = $this->getDoctrine()
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 404);
        }

        $permissions = $role->getPermissions();

        return $this->json([
            'permissions_count' => count($permissions),
            'permissions' => $permissions
        ]);
    }

    /**
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/token/aplikasis', methods: ['POST'])]
    public function showAplikasiFromToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
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
     */
    #[Route('/api/token/all_aplikasis', methods: ['POST'])]
    public function showAllAplikasiFromToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
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
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/token/permissions', methods: ['POST'])]
    public function showPermissionsByToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
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
     * @param string $id
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/jabatan_pegawais/{id}/roles', methods: ['GET'])]
    public function showRolesByJabatanPegawais(string $id, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        /** @var JabatanPegawai $jabatanPegawai */
        $jabatanPegawai = $this->entityManager
            ->getRepository(JabatanPegawai::class)
            ->findOneBy(['id' => $id]);

        if (null === $jabatanPegawai) {
            return $this->json([
                'code' => 404,
                'error' => 'No jabatan record found'
            ], 404);
        }

        $roles = RoleUtils::getRolesFromJabatanPegawai($jabatanPegawai);

        if (empty($roles)) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this Jabatan Pegawai'
            ], 404);
        }

        return $this->json([
            'roles_count' => count($roles),
            'roles' => RoleUtils::createRoleDefaultResponseFromArrayOfRoles($roles, $iriConverter)
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
