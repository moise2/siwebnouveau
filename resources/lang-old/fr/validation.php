<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lignes de langue de validation
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes contiennent les messages d'erreur par défaut utilisés par
    | la classe de validation. Certaines de ces règles ont plusieurs versions telles
    | que les règles de taille. N'hésitez pas à modifier chacun de ces messages ici.
    |
    */
    'economies_actualites'=>'Actualités sur l\'économie.',
    'accepted' => 'Le champ :attribute doit être accepté.',
    'active_url' => "Le champ :attribute n'est pas une URL valide.",
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne peut contenir que des lettres, des chiffres, des tirets et des traits de soulignement.',
    'alpha_num' => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',

    'between' => [
        'numeric' => 'Le champ :attribute doit être compris entre :min et :max.',
        'file' => 'Le champ :attribute doit être compris entre :min et :max kilo-octets.',
        'string' => 'Le champ :attribute doit être compris entre :min et :max caractères.',
        'array' => 'Le champ :attribute doit contenir entre :min et :max éléments.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'date' => "Le champ :attribute n'est pas une date valide.",
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit comporter :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit comporter entre :min et :max chiffres.',
    'dimensions' => "Les dimensions de l'image du champ :attribute ne sont pas valides.",
    'distinct' => 'Le champ :attribute a une valeur en double.',
    'email' => "Le champ :attribute doit être une adresse e-mail valide.",
    'ends_with' => 'Le champ :attribute doit se terminer par une des valeurs suivantes : :values.',
    'exists' => 'Le champ :attribute sélectionné est invalide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'actualites' => 'Actualités',
    'gt' => [
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
        'file' => 'Le champ :attribute doit être supérieur à :value kilo-octets.',
        'string' => 'Le champ :attribute doit être supérieur à :value caractères.',
        'array' => 'Le champ :attribute doit contenir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'file' => 'Le champ :attribute doit être supérieur ou égal à :value kilo-octets.',
        'string' => 'Le champ :attribute doit être supérieur ou égal à :value caractères.',
        'array' => 'Le champ :attribute doit contenir au moins :value éléments.',
    ],
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le champ :attribute sélectionné est invalide.',
    'in_array' => "Le champ :attribute n'existe pas dans :other.",
    'integer' => 'Le champ :attribute doit être un entier.',
    'ip' => "Le champ :attribute doit être une adresse IP valide.",
    'ipv4' => "Le champ :attribute doit être une adresse IPv4 valide.",
    'ipv6' => "Le champ :attribute doit être une adresse IPv6 valide.",
    'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lt' => [
        'numeric' => 'Le champ :attribute doit être inférieur à :value.',
        'file' => 'Le champ :attribute doit être inférieur à :value kilo-octets.',
        'string' => 'Le champ :attribute doit être inférieur à :value caractères.',
        'array' => 'Le champ :attribute doit contenir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'file' => 'Le champ :attribute doit être inférieur ou égal à :value kilo-octets.',
        'string' => 'Le champ :attribute doit être inférieur ou égal à :value caractères.',
        'array' => 'Le champ :attribute ne doit pas contenir plus de :value éléments.',
    ],
    'max' => [
        'numeric' => 'Le champ :attribute ne peut pas être supérieur à :max.',
        'file' => 'Le champ :attribute ne peut pas être supérieur à :max kilo-octets.',
        'string' => 'Le champ :attribute ne peut pas être supérieur à :max caractères.',
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'numeric' => 'Le champ :attribute doit être d\'au moins :min.',
        'file' => 'Le champ :attribute doit être d\'au moins :min kilo-octets.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
    ],
    'not_in' => 'Le champ :attribute sélectionné est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => 'Le champ :attribute doit être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_if' => 'Le champ :attribute est obligatoire lorsque :other est :value.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other est dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values sont présents.',
    'required_without' => 'Le champ :attribute est obligatoire lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire lorsqu\'aucun de :values n\'est présent.',
    'same' => 'Les champs :attribute et :other doivent correspondre.',
    'size' => [
        'numeric' => 'Le champ :attribute doit être égal à :size.',
        'file' => 'Le champ :attribute doit être égal à :size kilo-octets.',
        'string' => 'Le champ :attribute doit contenir :size caractères.',
        'array' => 'Le champ :attribute doit contenir :size éléments.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'Le champ :attribute a déjà été pris.',
    'uploaded' => "Le téléchargement du champ :attribute a échoué.",
    'url' => "Le format de l'URL du champ :attribute est invalide.",
    'uuid' => 'Le champ :attribute doit être un UUID valide.',


    /*
    |--------------------------------------------------------------------------
    | Lignes de langue de validation personnalisées
    |--------------------------------------------------------------------------
    |
    | Vous pouvez spécifier ici des messages de validation personnalisés pour les attributs en utilisant la
    | convention "attribute.rule" pour nommer les lignes. Cela permet de spécifier rapidement
    | une ligne de langue personnalisée spécifique pour une règle d'attribut donnée.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'message-personnalisé',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs de validation personnalisés
    |--------------------------------------------------------------------------
    |
    | Les lignes de langue suivantes sont utilisées pour remplacer notre espace réservé d'attribut
    | par quelque chose de plus convivial, comme "Adresse e-mail" au lieu
    | de "email". Cela nous aide simplement à rendre notre message plus expressif.
    |
    */

    'attributes' => [],

];
