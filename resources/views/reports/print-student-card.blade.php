<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <title>Student ID Card</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#fff; width: 560px;">

    {{-- ===== OUTER WRAPPER ===== --}}
    <table width="560" cellpadding="0" cellspacing="0" style="width: 560px;">

        {{-- ===== FRONT CARD ===== --}}
        <tr>
            <td style="padding: 20px 30px 0 30px;">
                <table width="500" cellpadding="0" cellspacing="0"
                    style="width: 500px; border: 3px solid #58666e; border-bottom: 5px solid #279121; background:#fff;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#7266ba; border-bottom: 6px solid #279121; padding: 6px 10px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:50px; vertical-align:middle;">
                                        <img src="{{ public_path('assets/images/Logo.jpeg') }}"
                                             width="40" height="40" alt="Logo">
                                    </td>
                                    <td style="vertical-align:middle; color:#fff; font-weight:bold; font-size:15px;">
                                        FORCES SCHOOL &amp; COLLEGE SYSTEM
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 10px 8px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    {{-- Left: Details --}}
                                    <td style="width:68%; vertical-align:top; text-align:left; color:#58666e; padding-right:5px;">
                                        <div style="font-size:17px; font-weight:bold; text-transform:uppercase; margin-bottom:10px; color:#58666e;">
                                            {{ $student->FirstName }} {{ $student->LastName }}
                                        </div>
                                        <table width="100%" cellpadding="0" cellspacing="4">
                                            <tr>
                                                <td style="width:38%; font-weight:bold; font-size:11px; color:#58666e;">Roll No:</td>
                                                <td style="font-size:11px; color:#58666e;">{{ $student->RollNumber }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:38%; font-weight:bold; font-size:11px; color:#58666e;">Class:</td>
                                                <td style="font-size:11px; color:#58666e;">{{ $student->class->ClassName ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:38%; font-weight:bold; font-size:11px; color:#58666e;">Section:</td>
                                                <td style="font-size:11px; color:#58666e;">{{ $student->section->SectionName ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </td>

                                    {{-- Right: Photo + Sign --}}
                                    <td style="width:32%; vertical-align:top; text-align:center;">
                                        <img src="{{ $student->StudentPhotoPath ? public_path('storage/'.$student->StudentPhotoPath) : public_path('assets/images/staff_profile.jpg') }}"
                                             width="85" height="100"
                                             style="border:1px solid #ccc;" alt="Photo">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;">
                                            <tr>
                                                <td style="font-size:8px; color:#58666e; width:35%; text-align:left; vertical-align:bottom;">
                                                    Principal:
                                                </td>
                                                <td style="vertical-align:bottom;">
                                                    <span style="display:block; border-bottom:1px dashed #58666e; height:10px; width:100%;"></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>

        {{-- Spacer --}}
        <tr>
            <td style="height: 25px;"></td>
        </tr>

        {{-- ===== BACK CARD ===== --}}
        <tr>
            <td style="padding: 0 30px 20px 30px;">
                <table width="500" cellpadding="0" cellspacing="0"
                    style="width:500px; border:3px solid #58666e; background:#fff;">
                    <tr>
                        <td style="padding: 12px 10px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Father Name:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->FatherName }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Address:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->Is_Guardian == 1 ? $student->GuardianAddress : $student->CurrentAddress }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Contact#:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->FatherPhone }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Emergency#:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->Is_Guardian == 1 ? $student->GuardianPhone : $student->FatherPhone }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Date of Birth:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->DateOfBirth }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Blood Group:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->BloodGroup }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Issue Date:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ \Carbon\Carbon::parse($student->created_at)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="width:44%; font-weight:bold; font-size:11px; color:#58666e; padding:4px 2px;">Expiry Date:</td>
                                    <td style="font-size:11px; color:#58666e; padding:4px 2px;">{{ $student->ExpireDate }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

</body>
</html>