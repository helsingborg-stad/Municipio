@card([
            'color' => 'success',
            'heading' => 'Ajsing bajsing',
            'content' => 'Sorry ' . $model['name'] . ', du har inget kulturkort...',
            'buttons' => [
                    ['type' => 'filled', 'color' => 'primary', 'text' => 'Logga ut', 'href' => $model['logoutUrl']]
                ]
        ])
@endcard
