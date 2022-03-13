<?php

namespace Illusionist\Socialite\Two;

class WechatOfficialAccountProvider extends AbstractProvider
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['snsapi_userinfo'];

    /**
     * The "openid" of the wechat.
     *
     * @var string
     */
    protected $openId;

    /**
     * Set the "openid" of the wechat.
     *
     * @param  string $openId
     * @return $this
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token, $response = [])
    {
        $openId = $response['openid'] ?? $this->openId;

        if (in_array('snsapi_base', $this->scopes, true)) {
            return ['openid' => $openId];
        }

        $response = $this->getHttpClient()->get('https://api.weixin.qq.com/sns/userinfo', [
            'query' => [
                'access_token' => $token,
                'openid'       => $openId,
                'lang'         => 'zh_CN',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['openid'],
            'unionid'  => $user['unionid'] ?? null,
            'nickname' => $user['nickname'] ?? null,
            'avatar'   => $user['headimgurl'] ?? null,
            'name'     => null,
            'email'    => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        $fields['appid'] = $fields['client_id'];

        unset($fields['client_id']);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'appid'      => $this->clientId,
            'secret'     => $this->clientSecret,
            'code'       => $code,
            'grant_type' => 'authorization_code',
        ];
    }
}
