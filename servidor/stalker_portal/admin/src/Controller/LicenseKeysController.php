<?php

namespace Ministra\Admin\Controller;

use Ministra\Admin\Adapter\DataTableAdapter;
use Ministra\Admin\Repository\LicenseKeysRepository;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229;
use Ministra\Lib\SMACCode;
use Ministra\Lib\SMACCodeException;
use Ministra\Lib\SMACLicenseInvalidFormatException;
use Psr\Container\ContainerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Upload\File;
use Upload\Storage\FileSystem;
class LicenseKeysController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/about-license-keys');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function about_license_keys()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function license_keys_list(\Ministra\Admin\Repository\LicenseKeysRepository $repository, \Psr\Container\ContainerInterface $container)
    {
        $attribute = $this->getDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->getCodeFilters();
        $container->set('allUserStatuses', [['id' => '1', 'title' => $this->setLocalization('Turned On')], ['id' => '2', 'title' => $this->setLocalization('Turned Off')]]);
        $container->set('allStatuses', [['id' => \Ministra\Lib\SMACCode::STATUS_NOT_ACTIVATED, 'title' => $this->setLocalization(\Ministra\Lib\SMACCode::STATUS_NOT_ACTIVATED)], ['id' => \Ministra\Lib\SMACCode::STATUS_ACTIVATED, 'title' => $this->setLocalization(\Ministra\Lib\SMACCode::STATUS_ACTIVATED)], ['id' => \Ministra\Lib\SMACCode::STATUS_MANUALLY_ENTERED, 'title' => $this->setLocalization(\Ministra\Lib\SMACCode::STATUS_MANUALLY_ENTERED)], ['id' => \Ministra\Lib\SMACCode::STATUS_BLOCKED, 'title' => $this->setLocalization('BlockedLicense')], ['id' => \Ministra\Lib\SMACCode::STATUS_RESERVED, 'title' => $this->setLocalization(\Ministra\Lib\SMACCode::STATUS_RESERVED)]]);
        $container->set('allKeyTypes', [['id' => '1,2', 'title' => $this->setLocalization('Standard')], ['id' => '3,4', 'title' => $this->setLocalization('Advanced')]]);
        $devices = $repository->findForFilters('device');
        $container->set('allDevices', \array_filter(\array_map(function ($row) {
            return !empty($row['device']) ? ['id' => $row['device'], 'title' => $row['device']] : false;
        }, !empty($devices) ? $devices : [])));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getDropdownAttribute()
    {
        $attribute = [['name' => 'key_id', 'title' => $this->setLocalization('ID'), 'checked' => false], ['name' => 'code', 'title' => $this->setLocalization('License key'), 'checked' => true], ['name' => 'key_type', 'title' => $this->setLocalization('Key type'), 'checked' => true], ['name' => 'count_clear_attempts', 'title' => $this->setLocalization('Remaining clearings'), 'searchable' => false, 'checked' => true], ['name' => 'expire_date', 'title' => $this->setLocalization('Key expiration date'), 'searchable' => false, 'checked' => true], ['name' => 'key_status', 'title' => $this->setLocalization('Status'), 'checked' => true], ['name' => 'user_id', 'title' => $this->setLocalization('User'), 'checked' => true], ['name' => 'device', 'title' => $this->setLocalization('Device type'), 'checked' => true], ['name' => 'last_active', 'title' => $this->setLocalization('User activity'), 'checked' => true], ['name' => 'action', 'title' => $this->setLocalization('Actions'), 'checked' => true, 'searchable' => false], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true, 'searchable' => true]];
        return $attribute;
    }
    private function getCodeFilters()
    {
        $filters = [];
        if (\array_key_exists('filters', $this->data)) {
            if (\array_key_exists('key_type', $this->data['filters']) && (string) $this->data['filters']['key_type'] !== '0') {
                $filters['SUBSTRING(`code`, 2, 1) IN' . ((string) $this->data['filters']['key_type'] == '1' ? '(1, 2)' : '(3, 4)') . ' AND 1 '] = '1';
            }
            if (\array_key_exists('request', $this->data['filters']) && (string) $this->data['filters']['request'] !== '0') {
                $filters['request'] = $this->data['filters']['request'];
            }
            if (\array_key_exists('status', $this->data['filters']) && (string) $this->data['filters']['status'] !== '0') {
                $const = \strtoupper($this->data['filters']['status']);
                $filters['status'] = \constant("SMACCode::STATUS_{$const}");
            }
            if (\array_key_exists('device', $this->data['filters']) && (string) $this->data['filters']['device'] !== '0') {
                $filters['device'] = \urlencode($this->data['filters']['device']);
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $filters;
    }
    public function license_keys_list_json(\Symfony\Component\HttpFoundation\Request $request, \Ministra\Admin\Repository\LicenseKeysRepository $licenseRepository, \Ministra\Admin\Adapter\DataTableAdapter $dataTableAdapter, \Psr\Container\ContainerInterface $container, $local_use = false)
    {
        if (!$this->isAjax && $local_use === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $dataTableAdapter->setHavingColumns(['key_type'])->process();
        $data = $licenseRepository->getGridData(null, $dataTableAdapter);
        $response = ['data' => [], 'recordsTotal' => $data['total'], 'recordsFiltered' => $data['filter']];
        $response['data'] = \array_map(function ($row) {
            $row['status_flag'] = \strtolower(\str_replace(' ', '_', $row['key_status']));
            $row['added'] = \strtotime($row['added']) * ($this->isAjax ? 1000 : 1);
            $row['action'] = $row['key_status'] == \Ministra\Lib\SMACCode::STATUS_BLOCKED ? 'active' : 'block';
            if (\in_array($row['key_type'], [1, 2])) {
                $row['key_type'] = $this->setLocalization('Standard');
            } else {
                if (\in_array($row['key_type'], [3, 4])) {
                    $row['key_type'] = $this->setLocalization('Advanced');
                } else {
                    $row['key_type'] = $this->setLocalization('Invalid');
                }
            }
            $row['count_clear_attempts'] = $row['count_clear_attempts'] < 0 ? '-' : $row['count_clear_attempts'];
            $row['expire_date'] = $row['expire_date'] < 0 ? $this->setLocalization('Absent') : $row['expire_date'];
            $row['login'] = empty($row['login']) ? $row['user_id'] : $row['login'];
            $row['key_status'] = $row['key_status'] === \Ministra\Lib\SMACCode::STATUS_BLOCKED ? 'BlockedLicense' : $row['key_status'];
            $row['last_active'] = (int) $row['last_active'] > 0 ? $row['last_active'] : null;
            switch ($row['key_status']) {
                case \Ministra\Lib\SMACCode::STATUS_BLOCKED:
                    $row['action'] = 'active';
                    break;
                case \Ministra\Lib\SMACCode::STATUS_NOT_ACTIVATED:
                    $row['action'] = 'block';
                    break;
                case \Ministra\Lib\SMACCode::STATUS_RESERVED:
                    $row['action'] = 'reserved';
                    break;
                default:
                    $row['action'] = 'ignore';
            }
            $row['RowOrder'] = 'dTRow_' . $row['key_id'];
            return $row;
        }, $data['data']);
        $response['data'] = $this->setLocalization($response['data'], 'key_status');
        $error = '';
        if ($this->isAjax && !$local_use) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\JsonResponse($response, empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function upload_key_file()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $data['data'] = [];
        $error = $this->setLocalization('Upload failed');
        $storage = new \Upload\Storage\FileSystem('/tmp', true);
        $file = new \Upload\File('files', $storage);
        try {
            $file->upload();
            $result = \Ministra\Lib\SMACCode::importFile($file->getNameWithExtension(), \file_get_contents($file->getPath() . '/' . $file->getNameWithExtension()));
            @\unlink($file->getPath() . '/' . $file->getNameWithExtension());
            if ($result !== false) {
                $data['msg'] = $this->setLocalization('License keys is imported ') . \Ministra\Lib\SMACCode::$countImported;
                $error = '';
            }
        } catch (\Ministra\Lib\SMACLicenseInvalidFormatException $e) {
            $data['msg'] = $this->setLocalization('Invalid format for next license keys') . ': ' . $e->getLicensesAsString();
        } catch (\Ministra\Lib\SMACCodeException $e) {
            $data['msg'] = $this->setLocalization($e->getMessage());
        } catch (\Exception $e) {
            $data['msg'] = $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function toggle_key_status(\Symfony\Component\HttpFoundation\Request $request, \Ministra\Admin\Repository\LicenseKeysRepository $licenseKeysRepository)
    {
        $data = ['id' => [], 'action' => 'updateTableRow', 'data' => [], 'msg_list' => []];
        $ids = $request->get($request->get('group_key'), $request->get('id'));
        if ($idsData = \json_decode($ids)) {
            $ids = $idsData;
        }
        if (empty($ids)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $action = $this->postData['action'] ?: $request->get('action');
        $licenses = $licenseKeysRepository->updateLicensesStatus($action, $ids);
        $retData = ['data' => $licenses, 'id' => [], 'additional' => []];
        foreach ($licenses as $license) {
            $result = false;
            if (isset($license['is_updated']) && $license['is_updated']) {
                $result = true;
                $retData['id'][$license['id']] = 1;
            } else {
                $retData['id'][$license['id']] = -1;
                $retData['additional']['action'] = 'JSErrorModalBox';
                if (empty($retData['additional']['msg'])) {
                    $retData['additional']['msg'] = $this->setLocalization('Not changed') . ' id:' . $license['id'];
                } else {
                    $retData['additional']['msg'] .= ', ' . $license['id'];
                }
            }
            $retData['msg_list'][$license['key_id']] = $this->groupMessageList($license['id'], $result, $this->updateMessageTemplate());
        }
        $result = \count($retData['id']);
        $data['id'] = \array_filter($retData['id']);
        $error = false;
        if (empty($retData['id'])) {
            $hasError = $result !== \count($retData['id']);
            if (!$hasError) {
                $error = $retData['msg'] = $error = $this->setLocalization('Nothing to do');
            } else {
                $error = $retData['msg'] = $this->setLocalization('Some errors found');
            }
        } else {
            $retData['data'] = $this->setLocalization($licenses['data'], 'status');
            $changed = \array_filter($data['id'], function ($val) {
                return $val !== -1;
            });
            $retData['id'] = \array_keys($retData['id']);
            if (!empty($changed)) {
                $msg_str = 'id: ' . \implode(', ', \array_keys($changed));
                $retData['msg'] = $this->setLocalization('Keys {updchid} has been updated', '', $msg_str, ['{updchid}' => $msg_str]);
            }
        }
        $response = $this->generateAjaxResponse($retData, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function updateMessageTemplate()
    {
        return ['success' => ['status' => true, 'msg' => $this->setLocalization('Key id:{updid} updated')], 'failed' => ['status' => false, 'msg' => $this->setLocalization('Key id:{updid} not updated')], 'error' => ['status' => false, 'msg' => $this->setLocalization('Update key id:{updid} ended with an error')]];
    }
    public function check_license_keys(\Symfony\Component\HttpFoundation\Request $request, \Ministra\Admin\Repository\LicenseKeysRepository $licenseRepository, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541 $clearKeyService)
    {
        return $this->clear_license_keys($request, $licenseRepository, $clearKeyService, true, 'Updated');
    }
    public function clear_license_keys(\Symfony\Component\HttpFoundation\Request $request, \Ministra\Admin\Repository\LicenseKeysRepository $licenseRepository, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541 $clearKeyService, $onlyCheck = false, $clearMessage = 'Cleared')
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('util_operator_secret', null) || !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('operator_key', null)) {
            $data = $this->generateAjaxResponse([], $this->setLocalization('Operator identifiers is empty. Set options util_operator_secret and operator_key in config.ini'));
            return new \Symfony\Component\HttpFoundation\JsonResponse($data, 400);
        }
        $licenses = \json_decode($request->get('ids'), true);
        if (!$licenses) {
            $data = $this->generateAjaxResponse([], $this->setLocalization('Empty list'));
            return new \Symfony\Component\HttpFoundation\JsonResponse($data, 400);
        }
        $invalidFormat = [];
        $denied = [];
        $licenses = $licenseRepository->getByPks($licenses);
        $licensesClearList = [];
        foreach ($licenses as $license) {
            if (!\preg_match("/[\\d][\\d]\\w{10}/", $license['code'])) {
                $invalidFormat[] = ['license' => $license['code'], 'id' => $license['id'], 'message' => $this->setLocalization('Error. License key belongs to other service provider.')];
                continue;
            }
            if (!\is_numeric($license['code'][1]) || $license['code'][1] % 2 !== 0) {
                $denied[] = ['license' => $license['code'], 'id' => $license['id'], 'message' => $this->setLocalization('Error. License key belongs to other service provider.')];
                continue;
            }
            $licensesClearList[] = ['license' => $license['code'], 'id' => $license['id']];
        }
        if (\count($licenses) === \count($denied) + \count($invalidFormat)) {
            $data = $this->generateAjaxResponse(['data' => [\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::cc9d4d893feda63bd5ac4c4589d5537a => $denied, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::e2d6676c0246a566add9474e18901cda4 => $invalidFormat]], $this->setLocalization('Error. License key belongs to other service provider.'));
            return new \Symfony\Component\HttpFoundation\JsonResponse($data, 200);
        }
        $clearKeyService->E35a1242ef937b73fc0734d9582d9b5b6($licensesClearList);
        if ($onlyCheck) {
            $clearKeyService->p930fa5b89a77ccbd968d3f95b3e553c5();
        }
        $data = $clearKeyService->clear();
        if (null === $data || !\is_array($data)) {
            $message = 'Something went wrong. Check your internet connection and try again later.';
            switch ($data) {
                case \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::e19ebd5c18814dd7447a4052855fd50d:
                    $message = '';
                    break;
                case \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::z8b325443d37fcc05e470189d0512d69d:
                    $message = 'Connection server error';
                    break;
                case \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::ca35e3a779698f9553115982ef366cc5:
                    $message = 'Connection to database failed';
                    break;
                case \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::q5f18a52348f11c20781111b72529a5d1:
                    $message = 'Incorrect operator key or secret phrase';
                    break;
                case \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::j228a068ce6c544f3a95fc4a194cfe9ac:
                    $message = 'Empty';
                    break;
                case \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::M0694d152ee02af553d09b72a6b14bc26:
                    $message = 'Server error. Try again later.';
                    break;
            }
            return new \Symfony\Component\HttpFoundation\JsonResponse($this->generateAjaxResponse([], $this->setLocalization($message)), 500);
        }
        $clearKeyService->Y93b9f13b6512cbe957c195ff3093543b();
        return new \Symfony\Component\HttpFoundation\JsonResponse(['data' => [\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::cc9d4d893feda63bd5ac4c4589d5537a => \array_merge($denied, $this->addMessageForGroupLicenses($clearKeyService->s85f951c8f163e769eb76a48a2f1f74f2(), 'Error. License key belongs to other service provider.')), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::e2d6676c0246a566add9474e18901cda4 => $invalidFormat, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::I4c53d6e690c05c419896241c95229c1c => $this->addMessageForGroupLicenses($clearKeyService->z8229879848b6b7ba02adc3b83c0b7ef5(), $clearMessage), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::e4a04ae93e25928dfb6deea2e4f72307 => $this->addMessageForGroupLicenses($clearKeyService->ab6b20fe1e28ccebe168617b65ae2617(), $onlyCheck ? $clearMessage : "Skipped. License key don't have assigned user."), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::fc2672b2c3dc0bc0b7ef72008ca8c410 => $this->addMessageForGroupLicenses($clearKeyService->d945ca9f5177bfb6134ed2f04e22ce3a(), 'Error. Limit of clearings is exceeded.'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::A386630dc04a40bee6ff6db6cb7bbc727 => $this->addMessageForGroupLicenses($clearKeyService->g7c2594d36dd7f8e362ca97d4dfeed403(), 'Error. License key is expired.'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::N26ae08dc62752ac2f9736943daa1ff2b => $this->addMessageForGroupLicenses($clearKeyService->Y38c3655c40367c3431c5cab6cd208045(), 'Error. License key is blocked.')]], 200);
    }
    private function addMessageForGroupLicenses($list, $message)
    {
        $newList = [];
        foreach ($list as $item) {
            $newList[] = ['message' => $this->setLocalization($message), 'id' => $item['id'], 'license' => $item['license']];
        }
        return $newList;
    }
    private function getCodesFields()
    {
        return ['id' => 'S_C.`id` as `id`', 'code' => 'S_C.`code` as `code`', 'key_type' => 'IF(SUBSTRING(S_C.`code`, 2, 1) IN (1, 2), 1, 2) as `key_type`', 'request' => 'S_C.`request` as `request`', 'added' => 'S_C.`added` as `added`', 'status' => 'S_C.`status` as `status`', 'user_id' => 'S_C.`user_id` as `user_id`', 'device' => 'S_C.`device` as `device`'];
    }
}
