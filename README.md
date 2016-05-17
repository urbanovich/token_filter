Example:

    public function install()
    {
        $token_filter = new TokenFilter();
        $token_filter->active = true;
        $token_filter->title = 'title';
        $token_filter->name = 'token_name';
        $token_filter->content = 'replace content a token';

        if (!parent::install()
            || !$token_filter->save())
            return false;

        return true;
    }

    public function hookReplaceToken($params)
    {

        if(array_key_exists('token_name', $params['tokens']))
        {
            $token_name = &$params['tokens']['token_name'];


            $token_name['content'] = 'replace content a token';
        }

        return $params;
    }

    set to textarea [token_name]