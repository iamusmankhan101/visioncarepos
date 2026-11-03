<div style="margin-top: 8px; padding-top: 5px; border-top: 1px solid #ddd; page-break-inside: avoid; color: #000;">
    <div style="font-weight: 600; color: #48b2ee; font-size: 12px; margin-bottom: 3px;">
        <i class="fa fa-eye"></i> {{ $product_line['product_label'] ?? ($product_line['name'].' '.($product_line['product_variation'] ?? '').' '.($product_line['variation'] ?? '')) }}
        <span style="color: #555; font-weight: normal;">- Prescription{{ !empty($prescription_contact->name) ? ' - '.$prescription_contact->name : '' }}</span>
    </div>
    <table width="100%" style="border-collapse: collapse; table-layout: fixed;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <table width="100%" style="border-collapse: collapse; border: 1px solid #ccc; font-size: 9px;">
                    <thead>
                        <tr style="background-color: #eaf6fd;">
                            <th style="border: 1px solid #ccc; padding: 2px;"></th>
                            <th style="border: 1px solid #ccc; padding: 2px;">Sph.</th>
                            <th style="border: 1px solid #ccc; padding: 2px;">Cyl.</th>
                            <th style="border: 1px solid #ccc; padding: 2px;">Axis.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 2px; text-align: left;">Right Distance</th>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field1 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field2 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field3 ?? '' }}</td>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 2px; text-align: left;">Right Near</th>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field4 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field5 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field6 ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <table width="100%" style="border-collapse: collapse; border: 1px solid #ccc; font-size: 9px;">
                    <thead>
                        <tr style="background-color: #eaf6fd;">
                            <th style="border: 1px solid #ccc; padding: 2px;"></th>
                            <th style="border: 1px solid #ccc; padding: 2px;">Sph.</th>
                            <th style="border: 1px solid #ccc; padding: 2px;">Cyl.</th>
                            <th style="border: 1px solid #ccc; padding: 2px;">Axis.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 2px; text-align: left;">Left Distance</th>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field7 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field8 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field9 ?? '' }}</td>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 2px; text-align: left;">Left Near</th>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field10 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field11 ?? '' }}</td>
                            <td style="border: 1px solid #ccc; padding: 2px; text-align: center;">{{ $prescription_contact->custom_field12 ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>
