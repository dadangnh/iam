<?php


namespace App\Controller;


use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Core\Permission;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\Helper\AplikasiHelper;
use App\Helper\RoleHelper;
use Doctrine\Persistence\ManagerRegistry;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
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
    #[IsGranted('ROLE_USER')]
    public function getRoleByJabatanPegawai(Request $request,
                                            IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $idJabatan = $content['id_jabatan_pegawai'];

        /** @var JabatanPegawai $jabatanPegawai */
        return $this->findRoleFromIdJabatanPegawai($idJabatan, $iriConverter);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/get_aplikasi_by_token', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getAplikasiByToken(IriConverterInterface $iriConverter): JsonResponse
    {
        return $this->findAplikasiFromToken($iriConverter);
    }

    /**
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_aplikasi_by_role_name', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getAplikasiByRoleName(Request $request,
                                          IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
        }

        $listAplikasi = [];
        foreach (RoleHelper::getAplikasiByRole($role) as $aplikasi) {
            $listAplikasi[] = AplikasiHelper::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/roles/mapping', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getMappingByRole(Request $request): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $keyData = strtolower($content['key_data']);

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
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
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/get_all_aplikasi_by_token', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getAllAplikasiByToken(IriConverterInterface $iriConverter): JsonResponse
    {
        return $this->findAllAplikasiFromToken($iriConverter);
    }

    /**
     * This method provide all aplikasis by role_name, including unreleased one
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_all_aplikasi_by_role_name', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getAllAplikasiByRoleName(Request $request,
                                             IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
        }

        $listAplikasi = [];
        foreach (RoleHelper::getAllAplikasiByRole($role) as $aplikasi) {
            $listAplikasi[] = AplikasiHelper::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/get_permissions_by_token', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getPermissionsByToken(IriConverterInterface $iriConverter): JsonResponse
    {
        return $this->findPermissionsFromToken($iriConverter);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/get_permissions_by_role_name', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getPermissionsByRoleName(Request $request): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $role = $this->readRoleFromRoleNameRequest($request);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
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
    #[IsGranted('ROLE_USER')]
    public function showAplikasisFromRoleName(string $roleName,
                                              IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $role = $this->doctrine
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
        }

        $listAplikasi = [];
        foreach (RoleHelper::getAplikasiByRole($role) as $aplikasi) {
            $listAplikasi[] = AplikasiHelper::createReadableAplikasiJsonData($aplikasi, $iriConverter);
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
    #[IsGranted('ROLE_USER')]
    public function showAllAplikasisFromRoleName(string $roleName,
                                                 IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $role = $this->doctrine
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
        }

        $listAplikasi = [];
        foreach (RoleHelper::getAllAplikasiByRole($role) as $aplikasi) {
            $listAplikasi[] = AplikasiHelper::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @param string $roleName
     * @return JsonResponse
     */
    #[Route('/api/roles/{roleName}/permissions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showPermissionsByRoleName(string $roleName): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $role = $this->doctrine
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);

        if (null === $role) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this name'
            ], 204);
        }

        $permissions = $role->getPermissions();

        return $this->json([
            'permissions_count' => count($permissions),
            'permissions' => $permissions
        ]);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/token/aplikasis', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function showAplikasiFromToken(IriConverterInterface $iriConverter): JsonResponse
    {
        return $this->findAplikasiFromToken($iriConverter);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/token/all_aplikasis', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function showAllAplikasiFromToken(IriConverterInterface $iriConverter): JsonResponse
    {
        return $this->findAllAplikasiFromToken($iriConverter);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/token/permissions', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function showPermissionsByToken(IriConverterInterface $iriConverter): JsonResponse
    {
        return $this->findPermissionsFromToken($iriConverter);
    }

    /**
     * @param string $id
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    #[Route('/api/jabatan_pegawais/{id}/roles', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showRolesByJabatanPegawais(string $id,
                                               IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        return $this->findRoleFromIdJabatanPegawai($id, $iriConverter);
    }

    /**
     * Firewall to make sure every request have token
     * @return void
     */
    private function ensureUserLoggedIn(): void
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
    }

    /**
     * @param Request $request
     * @return Role|null
     * @throws JsonException
     */
    private function readRoleFromRoleNameRequest(Request $request): ?Role
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $roleName = $content['role_name'];

        return $this->doctrine
            ->getRepository(Role::class)
            ->findOneBy(['nama' => $roleName]);
    }

    /**
     * @param mixed $idJabatan
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    private function findRoleFromIdJabatanPegawai(mixed $idJabatan,
                                                  IriConverterInterface $iriConverter): JsonResponse
    {
        /** @var JabatanPegawai $jabatanPegawai */
        $jabatanPegawai = $this->doctrine
            ->getRepository(JabatanPegawai::class)
            ->findOneBy(['id' => $idJabatan]);

        if (null === $jabatanPegawai) {
            return $this->json([
                'code' => 404,
                'error' => 'No jabatan record found'
            ], 204);
        }

        $roles = RoleHelper::getRolesFromJabatanPegawai($jabatanPegawai);

        if (empty($roles)) {
            return $this->json([
                'code' => 404,
                'error' => 'No roles associated with this Jabatan Pegawai'
            ], 204);
        }

        return $this->json([
            'roles_count' => count($roles),
            'roles' => RoleHelper::createRoleDefaultResponseFromArrayOfRoles($roles, $iriConverter)
        ]);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    private function findAplikasiFromToken(IriConverterInterface $iriConverter): JsonResponse
    {
        $listAplikasi = [];
        $listRoles = $this->findRolesFromCurrentUser();

        foreach (RoleHelper::getAplikasiByArrayOfRoles($listRoles) as $aplikasi) {
            $listAplikasi[] = AplikasiHelper::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @return array
     */
    private function findRolesFromCurrentUser(): array
    {
        $this->ensureUserLoggedIn();

        $listOfPlainRoles = $this->getUser()?->getRoles();
        $listRoles = [];
        foreach ($listOfPlainRoles as $plainRole) {
            $role = $this->doctrine
                ->getRepository(Role::class)
                ->findOneBy(['nama' => $plainRole]);
            if (null !== $role) {
                $listRoles[] = $role;
            }
        }

        return $listRoles;
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    private function findAllAplikasiFromToken(IriConverterInterface $iriConverter): JsonResponse
    {
        $listAplikasi = [];
        $listRoles = $this->findRolesFromCurrentUser();

        foreach (RoleHelper::getAllAplikasiByArrayOfRoles($listRoles) as $aplikasi) {
            $listAplikasi[] = AplikasiHelper::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($listAplikasi),
            'aplikasi' => $listAplikasi,
        ]);
    }

    /**
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    private function findPermissionsFromToken(IriConverterInterface $iriConverter): JsonResponse
    {
        $this->ensureUserLoggedIn();

        $listOfPlainRoles = $this->getUser()?->getRoles();
        $uniquePermissions = $listPermissionsOnRoles = $listRoles = [];
        $uniquePermissionsCount = 0;
        foreach ($listOfPlainRoles as $plainRole) {
            $role = $this->doctrine
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
                    $iri = $iriConverter->getIriFromResource($permission);
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
}
