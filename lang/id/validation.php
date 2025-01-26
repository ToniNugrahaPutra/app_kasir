<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute harus diterima.',
    'accepted_if' => 'The :attribute harus diterima ketika :other adalah :value.',
    'active_url' => 'The :attribute bukan URL yang valid.',
    'after' => 'The :attribute harus tanggal setelah :date.',
    'after_or_equal' => 'The :attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => 'The :attribute harus hanya terdiri dari huruf.',
    'alpha_dash' => 'The :attribute harus hanya terdiri dari huruf, angka, dashes dan underscores.',
    'alpha_num' => 'The :attribute harus hanya terdiri dari huruf dan angka.',
    'array' => 'The :attribute harus array.',
    'ascii' => 'The :attribute harus hanya terdiri dari karakter alfanumerik tunggal dan simbol.',
    'before' => 'The :attribute harus tanggal sebelum :date.',
    'before_or_equal' => 'The :attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'The :attribute harus antara :min dan :max item.',
        'file' => 'The :attribute harus antara :min dan :max kilobytes.',
        'numeric' => 'The :attribute harus antara :min dan :max.',
        'string' => 'The :attribute harus antara :min dan :max karakter.',
    ],
    'boolean' => 'The :attribute harus true atau false.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password salah.',
    'date' => 'The :attribute bukan tanggal yang valid.',
    'date_equals' => 'The :attribute harus tanggal sama dengan :date.',
    'date_format' => 'The :attribute tidak cocok dengan format :format.',
    'decimal' => 'The :attribute harus memiliki :decimal tempat desimal.',
    'declined' => 'The :attribute harus ditolak.',
    'declined_if' => 'The :attribute harus ditolak ketika :other adalah :value.',
    'different' => 'The :attribute dan :other harus berbeda.',
    'digits' => 'The :attribute harus :digits digit.',
    'digits_between' => 'The :attribute harus antara :min dan :max digit.',
    'dimensions' => 'The :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => 'The :attribute field memiliki nilai duplikat.',
    'doesnt_end_with' => 'The :attribute tidak boleh diakhiri dengan salah satu dari: :values.',
    'doesnt_start_with' => 'The :attribute tidak boleh diawali dengan salah satu dari: :values.',
    'email' => 'The :attribute harus alamat email yang valid.',
    'ends_with' => 'The :attribute harus diakhiri dengan salah satu dari: :values.',
    'enum' => 'Pilihan :attribute tidak valid.',
    'exists' => 'Pilihan :attribute tidak valid.',
    'file' => 'The :attribute harus file.',
    'filled' => 'The :attribute field harus memiliki nilai.',
    'gt' => [
        'array' => 'The :attribute harus memiliki lebih dari :value item.',
        'file' => 'The :attribute harus lebih besar dari :value kilobytes.',
        'numeric' => 'The :attribute harus lebih besar dari :value.',
        'string' => 'The :attribute harus lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => 'The :attribute harus memiliki :value item atau lebih.',
        'file' => 'The :attribute harus lebih besar atau sama dengan :value kilobytes.',
        'numeric' => 'The :attribute harus lebih besar atau sama dengan :value.',
        'string' => 'The :attribute harus lebih besar atau sama dengan :value karakter.',
    ],
    'image' => 'The :attribute harus gambar.',
    'in' => 'Pilihan :attribute tidak valid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute harus integer.',
    'ip' => 'The :attribute harus IP yang valid.',
    'ipv4' => 'The :attribute harus IP yang valid.',
    'ipv6' => 'The :attribute harus IP yang valid.',
    'json' => 'The :attribute harus string JSON yang valid.',
    'lowercase' => 'The :attribute harus huruf kecil.',
    'lt' => [
        'array' => 'The :attribute harus memiliki kurang dari :value item.',
        'file' => 'The :attribute harus kurang dari :value kilobytes.',
        'numeric' => 'The :attribute harus kurang dari :value.',
        'string' => 'The :attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => 'The :attribute harus memiliki kurang dari :value item.',
        'file' => 'The :attribute harus kurang dari atau sama dengan :value kilobytes.',
        'numeric' => 'The :attribute harus kurang dari atau sama dengan :value.',
        'string' => 'The :attribute harus kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address' => 'The :attribute harus MAC yang valid.',
    'max' => [
        'array' => 'The :attribute harus memiliki kurang dari :max item.',
        'file' => 'The :attribute harus kurang dari atau sama dengan :max kilobytes.',
        'numeric' => 'The :attribute harus kurang dari atau sama dengan :max.',
        'string' => 'The :attribute harus kurang dari atau sama dengan :max karakter.',
    ],
    'max_digits' => 'The :attribute harus memiliki kurang dari :max digit.',
    'mimes' => 'The :attribute harus file tipe: :values.',
    'mimetypes' => 'The :attribute harus file tipe: :values.',
    'min' => [
        'array' => 'The :attribute harus memiliki setidaknya :min item.',
        'file' => 'The :attribute harus setidaknya :min kilobytes.',
        'numeric' => 'The :attribute harus setidaknya :min.',
        'string' => 'The :attribute harus setidaknya :min karakter.',
    ],
    'min_digits' => 'The :attribute harus memiliki setidaknya :min digit.',
    'multiple_of' => 'The :attribute harus kelipatan dari :value.',
    'not_in' => 'Pilihan :attribute tidak valid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute harus angka.',
    'password' => [
        'letters' => 'The :attribute harus memiliki setidaknya satu huruf.',
        'mixed' => 'The :attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'The :attribute harus memiliki setidaknya satu angka.',
        'symbols' => 'The :attribute harus memiliki setidaknya satu simbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'The :attribute field harus ada.',
    'prohibited' => 'The :attribute field dihalangi.',
    'prohibited_if' => 'The :attribute field dihalangi ketika :other adalah :value.',
    'prohibited_unless' => 'The :attribute field dihalangi kecuali :other adalah dalam :values.',
    'prohibits' => 'The :attribute field dihalangi :other dari ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => 'The :attribute field harus diisi.',
    'required_array_keys' => 'The :attribute field harus memiliki entri untuk: :values.',
    'required_if' => 'The :attribute field harus diisi ketika :other adalah :value.',
    'required_if_accepted' => 'The :attribute field harus diisi ketika :other diterima.',
    'required_unless' => 'The :attribute field harus diisi kecuali :other adalah dalam :values.',
    'required_with' => 'The :attribute field harus diisi ketika :values ada.',
    'required_with_all' => 'The :attribute field harus diisi ketika :values ada.',
    'required_without' => 'The :attribute field harus diisi ketika :values tidak ada.',
    'required_without_all' => 'The :attribute field harus diisi ketika tidak ada dari :values.',
    'same' => 'The :attribute dan :other harus cocok.',
    'size' => [
        'array' => 'The :attribute harus memiliki :size item.',
        'file' => 'The :attribute harus :size kilobytes.',
        'numeric' => 'The :attribute harus :size.',
        'string' => 'The :attribute harus :size karakter.',
    ],
    'starts_with' => 'The :attribute harus diawali dengan salah satu dari: :values.',
    'string' => 'The :attribute harus string.',
    'timezone' => 'The :attribute harus zona waktu yang valid.',
    'unique' => 'The :attribute sudah ada.',
    'uploaded' => 'The :attribute gagal diunggah.',
    'uppercase' => 'The :attribute harus huruf besar.',
    'url' => 'The :attribute harus URL yang valid.',
    'ulid' => 'The :attribute harus ULID yang valid.',
    'uuid' => 'The :attribute harus UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
