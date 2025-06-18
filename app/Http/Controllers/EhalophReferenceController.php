<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Response;

// use App\Http\Controllers\phplib;
// use App\Models\EhalophField;
use App\Models\EhalophRecord;
// use App\Models\Halophyte;
use App\Models\HalophyteReferenceLog;

use App\Models\EhalophReferencePivot;
use App\Models\EhalophReference;
// use DB;
use Illuminate\Support\Facades\Log;

class EhalophReferenceController extends Controller
{


    public static function get_ref_labels($just_editable = false) {

        $just_editable = false;      // show ALL fields for now

        if(!$just_editable) {
            $labels = [
                'PT' => 'Publication Type',
                'AU' => 'Authors',
                'BA' => 'Book Author',
                'BE' => 'Editors',
                'GP' => '[=Group Author]',
                'AF' => 'Author Full Name',
                'BF' => 'Book Authors Full Name',
                'CA' => 'Corporate Authors',
                'TI' => 'Title',
                'chapter_title' => 'Chapter Title',
                'edition' => 'Edition',
                'nationality' => 'Nationality',
                'SO' => 'Source',
                'SE' => 'Series',
                'BS' => 'Book Series Subtitle',
                'LA' => 'Language',
                'DT' => 'Document Type',
                'CT' => 'Conference Title',
                'CY' => 'Conference Date',
                'CL' => 'Conference Location',
                'SP' => 'Conference Sponsors',
                'HO' => 'Conference Host',
                'DE' => 'Author Keywords',
                'ID' => 'Keywords Plus',
                'AB' => 'Abstract',
                'C1' => 'Author Address',
                'RP' => 'Reprint Address',
                'EM' => 'E-mail Address',
                'RI' => 'ResearcherID Number',
                'OI' => 'ORCID Number',
                'FU' => 'Funding Agency and Grant Number',
                'FX' => 'Funding Text',
                'CR' => 'Cited References',
                'NR' => 'Cited Reference Count',
                'TC' => 'Times Cited',
                'Z9' => 'Total Times Cited Count (WoS, BCI, and CSCD)',
                'PU' => 'Publisher',
                'PI' => 'Publisher City',
                'PA' => 'Publisher Address',
                'SN' => 'ISSN',
                'BN' => 'ISBN',
                'J9' => '29-Character Source Abbreviation',
                'JI' => 'ISO Source Abbreviation',
                'PD' => 'Publication Date',
                'PY' => 'Year Published',
                'VL' => 'Volume',
                'IS' => 'Issue',
                'PN' => 'Part Number',
                'SU' => 'Supplement',
                'SI' => 'Special Issue',
                'MA' => 'Meeting Abstract',
                'BP' => 'Beginning Page',
                'EP' => 'Ending Page',
                'AR' => 'Article Number',
                'DI' => 'Digital Object Identifier (DOI)',
                'D2' => 'Book Digital Object Identifier (DOI)',
                'PG' => 'Page Count',
                'WC' => 'Web of Science Category',
                'SC' => 'Subject Category',
                'GA' => 'Document Delivery Number',
                'UT' => 'Unique Article Identifier',
                'file' => 'File',
                'capa' => 'Cover',
                'link' => 'Link',
                'original_aronson' => 'Aronsons\'s Original Reference',
                'original_aronson_id' => 'Aronsons\'s Original Reference ID',
                'associados' => 'Plants associated with this reference',
                'privacidade' => 'Privacy settings',
            ];
        } else {
            $labels = [
                'AU' => 'Authors',
                'TI' => 'Title',
                'PU' => 'Publisher',
                'SN' => 'ISSN',
                'BN' => 'ISBN',
                'PY' => 'Year Published',
                'VL' => 'Volume',
                'IS' => 'Issue',
                'BP' => 'Beginning Page',
                'EP' => 'Ending Page',
                'DI' => 'Digital Object Identifier (DOI)',
                'D2' => 'Book Digital Object Identifier (DOI)',
                'UT' => 'Unique Article Identifier',
                'link' => 'Link',
                'RI' => 'ResearcherID Number',
                'EM' => 'E-mail Address',
                'LA' => 'Language',
            ];

        }

        return $labels;
    }

    public static function getAllReferences() {
        $refs = EhalophReference::select('TI')->get();

        return $refs;
    }

    public static function saveReference($plantID, $field_id, $ref_id, $cardNo = 0)
    {

        EhalophReferencePivot::updateOrCreate(
            [    'plantID' => $plantID,
                 'field_associated' => $field_id,
                 'field_associatedID' => $cardNo,
                 'referenceID' => $ref_id
            ],
            ['updated_at' => time()]
        );

    }


    public static function getReferencesByField($plantID)
    {
        $refs = EhalophReferencePivot::where('plantID', '=', $plantID)->get();
        $refs = $refs->toArray();

        return $refs;
    }



    public static function getReferences($plantID, $include_taxas_associados = true, $unique = false)
    {


        $a = EhalophRecord::find($plantID);

        if(!$a) {
            return collect();
        }


        // read pivot table and get all (distinct) referenceID's
         $refs = $a->references->sortBy('AU');           // sort by Author

        if($unique) {    // don't return duplicate references
            $refs = $refs->unique('id_pub');
        }

        $ret              = [];
        $taxas_associados = [];


        $idx = 0;
        foreach ($refs as $refRow) {

            $ret[$idx]['AU']            = $refRow['AU'];
            $ret[$idx]['ANO']           = $refRow['PY'];
            $ret[$idx]['TITULO']        = $refRow['TI'];
            $ret[$idx]['REVISTA']       = $refRow['SO'];
            $ret[$idx]['VOLUME']        = $refRow['VL'];
            $ret[$idx]['NUMERO']        = $refRow['IS'];
            $ret[$idx]['BP']            = $refRow['BP'];
            $ret[$idx]['EP']            = $refRow['EP'];
            $ret[$idx]['CHAPTER_TITLE'] = $refRow['chapter_title'];
            $ret[$idx]['FILE']          = $refRow['file'];
            $ret[$idx]['LINK']          = $refRow['link'];
            $ret[$idx]['PUBLICO']       = $refRow['publico'];

            $ret[$idx]['ID_REFERENCE'] = $refRow['id_pub'];

            $ret[$idx]['LINK']               = $refRow['link'];
            $ret[$idx]['MB_STRIMWIDTH_LINK'] = mb_strimwidth($refRow['link'], 0, 75, "...");

            $ja_ids = [];

            if($include_taxas_associados) {
                $taxas_associados = [];
                $refid = $refRow['id_pub'];

                $plants = EhalophReference::select('id')->where('id_pub', $refid);

                $present = [];
                foreach($plants as $plant) {
                    if($plant != $plantID) {                       // not this plant
                        if(!in_array($plant, $present)) {
                            $values = EhalophRecordController::getGenusAndSpecies($plant);
                            $taxas_associados[] = array(
                                'genus' => $values['genus'], 'species' => $values['species'], 'id' => $plant
                            );
                            $present[] = $plant;
                        }
                    }
                }
            }


            $ret[$idx]['TAXAS_ASSOCIADOS'] = $taxas_associados;
            $ret[$idx]['JA_IDS']           = $ja_ids;

            $idx++;

        }

        return ($ret);
    }

    // NOT USED: Livewire was calling this multiple times so I have now implemented an Ajax call that calls refselected_ajax()
    public static function add_to_reference_log($user_id, $ref_id, $field_id = 0) {

        $ret = HalophyteReferenceLog::firstOrNew(
            ['user_id' => $user_id, 'ref_id' => $ref_id, 'field_id' => $field_id],
            ['updated_at' => time()]
        );
        $ret->updated_at = time();
        $ret->save();
    }

    public static function refselected_ajax(Request $request) {

        // Log::debug("Ajax call refselected");

        $data = $request;

        $user_id = Auth::id();
        $ref_id = $data->ref_id;
        $field_id = $data->field_id;

        $ret = HalophyteReferenceLog::firstOrNew(
            ['user_id' => $user_id, 'ref_id' => $ref_id, 'field_id' => $field_id],
            ['updated_at' => time()]
        );
        $ret->updated_at = time();
        $ret->save();


    }





    public static function get_reference_log($user_id, $field_id = 0) {

        if($field_id > 0) {
            $refs = EhalophReference::select('ehaloph_references.*')
                ->leftjoin('halophyte_references_log', 'ehaloph_references.id_pub', '=', 'halophyte_references_log.ref_id')
                ->where(['halophyte_references_log.user_id' => $user_id, 'halophyte_references_log.field_id' => $field_id]);
        } else {
            $refs = EhalophReference::select('ehaloph_references.*')
                ->leftjoin('halophyte_references_log', 'ehaloph_references.id_pub', '=', 'halophyte_references_log.ref_id')
                ->where(['halophyte_references_log.user_id' => $user_id]);

        }

        return($refs);

    }


    public static function reference_edit($id = 0) {

        return(view('frontend.reference_edit', ['id' => $id]));

    }



} // end of class
