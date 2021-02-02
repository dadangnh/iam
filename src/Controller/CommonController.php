<?php


namespace App\Controller;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Core\Role;
use App\Entity\Pegawai\JabatanPegawai;
use App\utils\AplikasiUtils;
use App\utils\RoleUtils;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
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
     * @Route("/", name="app_index")
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('api_entrypoint', [], 301);
    }

    /**
     * @Route("/api/get_roles_by_jabatan_pegawai", methods={"POST"})
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     * @throws JsonException
     */
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
     * @Route("/api/get_aplikasi_by_token", methods={"POST"})
     * @param Request $request
     * @param IriConverterInterface $iriConverter
     * @return JsonResponse
     */
    public function getAplikasiByToken(Request $request, IriConverterInterface $iriConverter): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $listOfPlainRoles = $this->getUser()->getRoles();
        $lisAplikasi = $listRoles = [];
        foreach ($listOfPlainRoles as $plainRole) {
            $role = $this->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['nama' => $plainRole]);
            if (null !== $role) {
                $listRoles[] = $role;
            }
        }

        foreach (RoleUtils::getAplikasiByArrayOfRoles($listRoles) as $aplikasi) {
            $lisAplikasi[] = AplikasiUtils::createReadableAplikasiJsonData($aplikasi, $iriConverter);
        }

        return $this->json([
            'aplikasi_count' => count($lisAplikasi),
            'aplikasi' => $lisAplikasi,
        ]);
    }
}
