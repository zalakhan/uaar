<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class Tender extends Model

{

    public const CATEGORY_SELECT = '0';



    protected $fillable = [

        'category',

        'title',

        'tender_no',

        'description',

        'uploaded_date',

        'due_date',

        'tender_file',

    ];



    protected function casts(): array

    {

        return [

            'uploaded_date' => 'date',

            'due_date' => 'date',

        ];

    }



    /**

     * Available tender categories (value => label).

     *

     * @return array<string, string>

     */

    public static function categories(): array

    {

        return [

            'Purchase' => 'Tender (P&IC)',

            'Auction' => 'Auction',

            'Tender' => 'Tender (Works)',

            'Quotation' => 'Quotation',

            'Pre-Qualification' => 'Pre-Qualification',

        ];

    }



    /**

     * Categories that use the title field.

     */

    public static function usesTitleFields(string $category): bool

    {

        return in_array($category, ['Purchase', 'Auction'], true);

    }



    /**

     * Categories that use tender number and description fields.

     */

    public static function usesDetailFields(string $category): bool

    {

        return in_array($category, ['Tender', 'Quotation', 'Pre-Qualification'], true);

    }



    /**

     * Human-readable category label.

     */

    public function categoryLabel(): string

    {

        return self::categories()[$this->category] ?? $this->category;

    }



    /**

     * Sanitized HTML description safe for rendering.

     */

    public function sanitizedDescription(): string

    {

        return clean($this->description ?? '', 'news');

    }

}

