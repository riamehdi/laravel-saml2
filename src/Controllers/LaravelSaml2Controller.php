<?php

namespace DaVikingCode\LaravelSaml2\Controllers;

use App\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Helper;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\Response;
use LightSaml\SamlConstants;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Credential\X509Certificate;
use LightSaml\Credential\KeyHelper;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Binding\BindingFactory;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\XmlDSig\SignatureXmlReader;


class LaravelSaml2Controller extends Controller
{
    private $idp_logout_url;
    private $idp_login_url;
    private $sp_entity_id;
    private $sp_sls_url; // not used
    private $sp_acs_url;
    private $sp_cert_file;
    private $sp_key_file;
    private $idp_cert_file;

    public function __construct()
    {
        // environment
        if(App::environment() == 'production')
        {
            $this->sp_entity_id = config('laravelsaml2.sp_entity_id_prod');
            $this->idp_logout_url = config('laravelsaml2.idp_logout_url_prod') . $this->sp_entity_id;
            $this->idp_login_url = config('laravelsaml2.idp_login_url_prod');
        }
        else // dev or local
        {
            $this->sp_entity_id = config('laravelsaml2.sp_entity_id_dev');
            $this->idp_logout_url = config('laravelsaml2.idp_logout_url_dev') . $this->sp_entity_id;
            $this->idp_login_url = config('laravelsaml2.idp_login_url_dev');
        }
        $this->sp_acs_url = route('saml2-acs');

        $this->sp_cert_file = storage_path(config('laravelsaml2.cert_path') . '/' . config('laravelsaml2.sp_cert_file'));
        $this->sp_key_file = storage_path(config('laravelsaml2.cert_path') . '/' . config('laravelsaml2.sp_key_file'));
        $this->idp_cert_file = storage_path(config('laravelsaml2.cert_path') . '/' . config('laravelsaml2.idp_cert_file'));

        // logging
        Config::set('logging.channels.' . config('laravelsaml2.log_channel') . '.driver', config('laravelsaml2.log_driver'));
        Config::set('logging.channels.' . config('laravelsaml2.log_channel') . '.path', config('laravelsaml2.log_path'));
        Config::set('logging.channels.' . config('laravelsaml2.log_channel') . '.ignore_exceptions', config('laravelsaml2.log_ignore_exceptions'));
        Config::set('logging.channels.' . config('laravelsaml2.log_channel') . '.level', config('laravelsaml2.log_level'));
        Config::set('logging.channels.' . config('laravelsaml2.log_channel') . '.days', config('laravelsaml2.log_days'));
    }

    public function login()
    {
        // Connection via Saml2 to Alex.v2.2, redircets to Acs() after login.

        $authnRequest = new AuthnRequest();
        $authnRequest
            ->setAssertionConsumerServiceURL($this->sp_acs_url)
            ->setProtocolBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination($this->idp_login_url)
            ->setIssuer(new Issuer($this->sp_entity_id));

        $certificate = X509Certificate::fromFile($this->sp_cert_file);
        $privateKey = KeyHelper::createPrivateKey($this->sp_key_file, '', true);
        $authnRequest->setSignature(new SignatureWriter($certificate, $privateKey));

        $serializationContext = new SerializationContext();
        $authnRequest->serialize($serializationContext->getDocument(), $serializationContext);

        $bindingFactory = new BindingFactory();
        $postBinding = $bindingFactory->create(SamlConstants::BINDING_SAML2_HTTP_POST);

        $messageContext = new MessageContext();
        $messageContext->setMessage($authnRequest);

        /** @var \Symfony\Component\HttpFoundation\Response $httpResponse */
        $httpResponse = $postBinding->send($messageContext);
        print $httpResponse->getContent();
    }

    public function acs()
    {
        $request = Request::createFromGlobals();
        $xml = base64_decode($request->get('SAMLResponse'));

        // deserialize XML into a Response data model object
        $deserializationContext = new DeserializationContext();
        $deserializationContext->getDocument()->loadXML($xml);

        dd($deserializationContext);

        // deserialize signature
        $signatureReader = new SignatureXmlReader();
        foreach ($deserializationContext->getDocument()->firstChild->childNodes as $node) {
            // do something with this node
            if($node->nodeName == 'saml:Assertion')
            {
                foreach ($node->childNodes as $c_node) {
                    if($c_node->nodeName == 'ds:Signature') {
                        $signatureReader->deserialize($c_node, $deserializationContext);
                        break;
                    }
                }
            }
        }

        // Verify signature
        $key = KeyHelper::createPublicKey(X509Certificate::fromFile($this->idp_cert_file));
        /** @var \LightSaml\Model\XmlDSig\SignatureXmlReader $signatureReader */
        try {
            if ($signatureReader->validate($key)) {
                // Signature OK : deserialize response
                $response = new Response();
                $response->deserialize(
                    $deserializationContext->getDocument()->firstChild,
                    $deserializationContext
                );

                // use decrypted assertion
                $assertion = $response->getFirstAssertion();

                // load attributes in associative array
                $attributes = [];
                foreach ($assertion->getFirstAttributeStatement()->getAllAttributes() as $attribute) {
                    $attributes[$attribute->getName()] = $attribute->getFirstAttributeValue();
                }
                return app('App\Http\Controllers\Api\UserController')->logUserIn($attributes); // send attributes to app login function

            } else {
                abort(401, 'Signature not validated');
            }
        } catch (\Exception $ex) {
            abort(401, 'Signature validation failed');
        }
    }

    public function logout()
    {
        Auth::logout();

        // disconnect from Alex v2.2
        return redirect($this->idp_logout_url);
    }

    public function getMetadata()
    {
        $entityDescriptor = new EntityDescriptor();
        $entityDescriptor
            ->setID(Helper::generateID())
            ->setEntityID($this->sp_entity_id)
        ;

        $entityDescriptor->addItem(
            $spSsoDescriptor = (new SpSsoDescriptor())
                ->setWantAssertionsSigned(true)
                ->setAuthnRequestsSigned(true)
        );

        $spSsoDescriptor->addKeyDescriptor(
            $keyDescriptor = (new KeyDescriptor())
                ->setUse(KeyDescriptor::USE_SIGNING)
                ->setCertificate(X509Certificate::fromFile($this->sp_cert_file))
        );

        $spSsoDescriptor->addKeyDescriptor(
            $keyDescriptor = (new KeyDescriptor())
                ->setUse(KeyDescriptor::USE_ENCRYPTION)
                ->setCertificate(X509Certificate::fromFile($this->sp_cert_file))
        );

        // No SLS
//        $spSsoDescriptor->addSingleLogoutService(
//            $sls = (new SingleLogoutService())
//                ->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
//                ->setLocation($this->sp_sls_url)
//        );

        $spSsoDescriptor->addNameIDFormat('urn:oasis:names:tc:SAML:2.0:nameid-format:transient');

        $spSsoDescriptor->addAssertionConsumerService(
            $acs = (new AssertionConsumerService())
                ->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST)
                ->setLocation($this->sp_acs_url)
                ->setIsDefault(true)
        );

        $serializationContext = new SerializationContext();
        $entityDescriptor->serialize($serializationContext->getDocument(), $serializationContext);

        $raw = $serializationContext->getDocument()->saveXML();
        $xml = new \DOMDocument();
        $xml->loadXML($raw);
        $raw_without_prolog = $xml->saveXML($xml->documentElement);
        $prolog = '<?xml version="1.0" encoding="UTF-8"?>';
        $new_xml = new \DOMDocument();
        $new_xml->formatOutput = true;
        $new_xml->loadXML($prolog . $raw_without_prolog);

        return response($new_xml->saveXML(), 200, [
            "Content-Type" => "application/xml; charset=utf-8"
        ]);
    }
}
