<x-mail::message>
    # Hi,

    This is to notify you about the warning status of your ingredients in stock. please check the ingredients below and order more if necessary.

    <x-mail::table>
        | Ingredient Name                      | Stock Capacity                             |              Available Stock(%)     | Stock Level                     | Unit |
        |:------------------------------------:|-------------------------------------------:|-----------------------------------:|--------------------------------:|:-------------------|
        @foreach($ingredients as $ingredient)
            | {{ $ingredient['ingredient_name'] }} | {{ $ingredient['ingredient_max_stock'] }} | {{ $ingredient['stock_balance'] }} | {{ $ingredient['stock_level'] }} | {{ $ingredient['unit'] }} |
        @endforeach
    </x-mail::table>

    Thanks,
    {{ config('app.name') }}
</x-mail::message>




