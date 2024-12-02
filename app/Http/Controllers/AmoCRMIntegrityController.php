<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\ContactModel;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\LeadModel;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Models\NoteType\ServiceMessageNote;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Helpers\EntityTypesInterface;

define('TOKEN_FILE', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json');

class AmoCRMIntegrityController extends Controller
{
    public function bootstrap()
    {
        $clientId = env('AMO_CLIENT_ID');
        $clientSecret = env('AMO_CLIENT_SECRET');
        $redirectUri = env('AMO_CLIENT_REDIRECT_URI');

        return new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
    }

    //Error Printer

    public function printError(AmoCRMApiException $e): void
    {
        $errorTitle = $e->getTitle();
        $code = $e->getCode();
        $debugInfo = var_export($e->getLastRequestInfo(), true);

        $error = <<<EOF
        Error: $errorTitle
        Code: $code
        Debug: $debugInfo
        EOF;

        echo '<pre>' . $error . '</pre>';
    }

    //End Error Printer

    //Token Actions

    /**
     * @param array $accessToken
     */
    public function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents(TOKEN_FILE, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    /**
     * @return AccessToken
     */
    public function getToken()
    {
        if (!file_exists(TOKEN_FILE)) {
            exit('Access token file not found');
        }

        $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
    //End Token Actions

    public function get_token()
    {
        session_start();
        $apiClient = $this->bootstrap();

        if (isset($_GET['referer'])) {
            $apiClient->setAccountBaseDomain($_GET['referer']);
        }


        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;
            if (isset($_GET['button'])) {
                echo $apiClient->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );
                die;
            } else {
                $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
                header('Location: ' . $authorizationUrl);
                die;
            }
        } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }

        /**
         * Ловим обратный код
         */
        try {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

            if (!$accessToken->hasExpired()) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $apiClient->getAccountBaseDomain(),
                ]);
            }
        } catch (Exception $e) {
            die((string)$e);
        }

        $ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

        printf('Hello!');
        var_dump($ownerDetails);
        return;
    }

    public function leads_list() {
        $apiClient = $this->bootstrap();

        $accessToken = $this->getToken();

        $apiClient->setAccessToken($accessToken)
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
        ->onAccessTokenRefresh(
        function (AccessTokenInterface $accessToken, string $baseDomain) {
            $this->saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $baseDomain,
            ]);
        });

        // $leadsService = $apiClient->leads();
        // $leadsCollection = $leadsService->get();

        $filter = new LeadsFilter();
        $filter->setOrder("updated_at", "desc");

        $leads = $apiClient->leads()->get($filter, [LeadModel::CONTACTS]);
        return $leads;
    }

    public function add_lead($lead_data) {
        $apiClient = $this->bootstrap();

        $name = $lead_data['NAME'];
        $phone = $lead_data['PHONE'];
        $email = $lead_data['EMAIL'];
        $leadName = $lead_data['LEAD_NAME'];

        $accessToken = $this->getToken();

        $apiClient->setAccessToken($accessToken)
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
        ->onAccessTokenRefresh(
        function (AccessTokenInterface $accessToken, string $baseDomain) {
            $this->saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $baseDomain,
            ]);
        }
        );

        $leadsService = $apiClient->leads();

        try {
        $contacts = $apiClient->contacts()->get((new ContactsFilter())->setQuery($phone));
        $contact = $contacts[0];
        } catch(AmoCRMApiException $e) {
        $contact = new ContactModel();
        $contact->setName($name);

        $CustomFieldsValues = new CustomFieldsValuesCollection();
        $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
        $emailField->setValues((new MultitextCustomFieldValueCollection())->add((new MultitextCustomFieldValueModel())->setEnum('WORK')->setValue($email)));
        $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
        $phoneField->setValues((new MultitextCustomFieldValueCollection())->add((new MultitextCustomFieldValueModel())->setEnum('WORK')->setValue($phone)));

        $CustomFieldsValues->add($emailField);
        $CustomFieldsValues->add($phoneField);

        $contact->setCustomFieldsValues($CustomFieldsValues);

        try {
            $contactModel = $apiClient->contacts()->addOne($contact);
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }
        }

        // Создаем сделку
        $lead = new LeadModel();
        $lead->setName($leadName)->setContacts((new ContactsCollection())->add(($contact)));
        if(isset($lead_data['PIPELINEID']) && $lead_data['PIPELINEID'] != null)
        {
            $pipe = $lead_data['PIPELINEID'];
            $lead->setPipelineId($pipe);
        }

        $CustomFieldsValues = new CustomFieldsValuesCollection();

        $lead->setCustomFieldsValues($CustomFieldsValues);
        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);

        try {
        $leadsCollection = $leadsService->add($leadsCollection);
        $lead_id = $leadsCollection[0]->id;

        return $lead_id;
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }
    }

    public function add_contact_to_lead($contact_data) {
        $apiClient = $this->bootstrap();

        $lead_id = $contact_data['LEAD_ID'];
        $name = $contact_data['NAME'];
        $phone = $contact_data['PHONE'];
        $comment = $contact_data['COMMENT'];

        $accessToken = $this->getToken();

        $apiClient->setAccessToken($accessToken)
        ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
        ->onAccessTokenRefresh(
        function (AccessTokenInterface $accessToken, string $baseDomain) {
            $this->saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $baseDomain,
            ]);
        });

        $contact = new ContactModel();
        $contact->setName($name);
        $CustomFieldsValues = new CustomFieldsValuesCollection();
        $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
        $phoneField->setValues((new MultitextCustomFieldValueCollection())->add((new MultitextCustomFieldValueModel())->setEnum('WORK')->setValue($phone)));
        $CustomFieldsValues->add($phoneField);
        $contact->setCustomFieldsValues($CustomFieldsValues);

        $contactModel = $apiClient->contacts()->addOne($contact);
        $contact_id = $contactModel->getId();

        $notesCollection = new NotesCollection();
        $serviceMessageNote = new ServiceMessageNote();
        $serviceMessageNote
            ->setEntityId($contactModel->getId())
            ->setText($comment)
            ->setService('Api Library');

        $notesCollection = $notesCollection->add($serviceMessageNote);
        $leadNotesService = $apiClient->notes(EntityTypesInterface::CONTACTS);
        $leadNotesService->add($notesCollection);

        $lead = $apiClient->leads()->getOne($lead_id);

        $links = new LinksCollection();
        $links->add($lead);

        try {
            $apiClient->contacts()->link($contactModel, $links);
            return "result_succese";
        } catch (AmoCRMApiException $e) {
            // printError($e);
            // die;
            return "result_error";
        }
    }
}
