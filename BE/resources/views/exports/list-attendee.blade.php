<?php 
    $amountSeriesConvention200 = 0;
    $amountSeriesConvention700 = 0;
    $amountStatusItemCd100 = 0;
    $amountStatusItemCd200 = 0;
    $amountDefinitionValue10 = 0;
    $amountDefinitionValue20 = 0;
    $amountDefinitionValue30 = 0;
    $amountStatusItemCd400 = 0;
    $amountStatusItemCd500 = 0;
    $amountStatusItemCd600 = 0;
    $amountStatusItemCd700 = 0;
    $amountStatusItemCd800 = 0;
    $amountStatusItemCd900 = 0;
    $amountFollowDate = 0;
    foreach ($attendees as $key => $attendee) {
        if ($attendee->series_convention_200) {
            $amountSeriesConvention200 += 1;
        }
        if ($attendee->series_convention_700) {
            $amountSeriesConvention700 += 1;
        }
        if ($attendee->status_item_cd_100) {
            $amountStatusItemCd100 += 1;
        }
        if ($attendee->status_item_cd_200) {
            $amountStatusItemCd200 += 1;
        }
        if ($attendee->definition_value_10) {
            $amountDefinitionValue10 += 1;
        }
        if ($attendee->definition_value_20) {
            $amountDefinitionValue20 += 1;
        }
        if ($attendee->definition_value_30) {
            $amountDefinitionValue30 += 1;
        }
        if ($attendee->status_item_cd_400) {
            $amountStatusItemCd400 += 1;
        }
        if ($attendee->status_item_cd_500) {
            $amountStatusItemCd500 += 1;
        }
        if ($attendee->status_item_cd_600) {
            $amountStatusItemCd600 += 1;
        }
        if ($attendee->status_item_cd_700) {
            $amountStatusItemCd700 += 1;
        }
        if ($attendee->status_item_cd_800) {
            $amountStatusItemCd800 += 1;
        }
        if ($attendee->status_item_cd_900) {
            $amountStatusItemCd900 += 1;
        }
        if ($attendee->follow_date) {
            $amountFollowDate += 1;
        }
    }
?>
<tr>
    <td style="font-weight: 800">参加者明細</td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    <td></td>
    <td>{{ \Carbon\Carbon::now()->format('Y/m/d H:i') }} 時点</td>
    <td></td><td></td><td></td>
</tr>
<tr>
    <td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td>
    <td colspan="3" style="border: 2px solid #ff0000; color : #ff0000;font-size: 16px; font-weight: 800;text-align: center;">社外秘</td>
    <td></td>
    <td></td><td></td>
    <td></td><td></td><td></td><td></td>
    <td></td>
</tr>
<tr>
    <td></td><td></td><td></td><td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    <td>項目別集計</td><td></td><td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    <td>単位:人</td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountSeriesConvention200 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountSeriesConvention700 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd100 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd200 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountDefinitionValue10 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountDefinitionValue20 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountDefinitionValue30 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd400 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd500 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd600 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd700 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd800 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd900 ?? 0 }}</td>
    <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountFollowDate ?? 0 }}</td>
</tr>
<tr>
    <td></td><td></td><td></td><td></td><td></td><td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
</tr>
<table>
    <thead>
        <tr>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>№</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>担当者組織名</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>担当者ユーザコード</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>担当者名</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>施設コード</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center;width:300px;" rowspan="2"><br>施設名</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>所属部科コード</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>所属部科名</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>役職名</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>個人コード</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>個人名</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>役割</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2">前回<br>案内</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2">前回<br>出席</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2">企画時<br>出席予定</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>案内</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center;" colspan="3">
                出席予定
            </th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>宿泊</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>旅券</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>ﾁｹｯﾄ</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>出席</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2">情報<br>交換会</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>慰労会</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center" rowspan="2"><br>フォロー</th>
        </tr>
        <tr>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center">出席</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center">欠席</th>
            <th style="background:#daeef3;border:1px solid #000000;text-align:center">未定</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attendees as $key => $attendee)
        <tr>
            <td style="text-align: right;border:1px solid #000000;">{{ $key + 1 }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->org_label }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->user_cd }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->user_name }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->facility_cd }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->facility_short_name }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->department_cd }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->department_name }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->display_position_name ?? '' }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->person_cd }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->person_name }}</td>
            <td style="text-align: left;border:1px solid #000000;">{{ $attendee->definition_label }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->series_convention_200 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->series_convention_700 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_100 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_200 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->definition_value_10 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->definition_value_20 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->definition_value_30 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_400 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_500 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_600 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_700 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_800 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->status_item_cd_900 }}</td>
            <td style="text-align:center;border:1px solid #000000;">{{ $attendee->follow_date ? \Carbon\Carbon::parse($attendee->follow_date)->format('m/d') : '' }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="background-color: #ffffcc;border:1px solid #000000"></td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountSeriesConvention200 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountSeriesConvention700 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd100 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd200 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountDefinitionValue10 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountDefinitionValue20 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountDefinitionValue30 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd400 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd500 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd600 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd700 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd800 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountStatusItemCd900 }}</td>
            <td style="text-align:center;background-color: #ffffcc;border:1px solid #000000">{{ $amountFollowDate }}</td>
        </tr>
    </tbody>
</table>