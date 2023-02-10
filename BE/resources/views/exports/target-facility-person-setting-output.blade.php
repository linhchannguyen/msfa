<table>
    <thead>

        <tr>
            <th style="font-size: 18px; font-weight: bold;width:120px">ターゲット施設個人一覧</th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>

            @if(count($attendees['segmentType']) > 0)
                @foreach ($attendees['segmentType'] as $segmentType)
                    <th style="width:120px;text-align:center;"></th>
                @endforeach
            @endif
            
            @if(count($attendees['targetProduct']) > 0)
                <?php $lastItem = count($attendees['targetProduct']) - 2;  ?>
                @foreach ($attendees['targetProduct'] as $key => $targetProduct)
                    @if($lastItem == $key)
                        <th style="font-size: 12px;font-weight: bold;width:120px;text-align:right;" colspan="2">{{ $attendees['date_system_print']}} 時点</th>
                    @else
                        <th style="width:120px;text-align:center;"></th>
                    @endif
                @endforeach
            @endif

        </tr>

        <tr>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            <th style="width:120px;text-align:center;"></th>
            @if(count($attendees['segmentType']) > 0)
                @foreach ($attendees['segmentType'] as $segmentType)
                    <th style="width:120px;text-align:center;"></th>
                @endforeach
            @endif

            @if(count($attendees['targetProduct']) > 0)
                @foreach ($attendees['targetProduct'] as $targetProduct)
                <th style="width:120px;text-align:center;"></th>
                @endforeach
            @endif

        </tr>
        <tr>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
            @if(count($attendees['segmentType']) > 0)
                @foreach ($attendees['segmentType'] as $segmentType)
                    <th style="width:120px;text-align:center;background:#daeef3;border-top:1px solid black;border-right:1px solid black;"></th>
                @endforeach
            @endif
            
            @if(count($attendees['targetProduct']) > 0)
                 <th style="width:120px;text-align:center;background:#daeef3;border:1px solid black;" colspan="{{ count($attendees['targetProduct']) }}">ターゲット情報</th>
            @endif

        </tr>

        <tr>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">施設コード</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">施設名</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">所属部科コード</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">所属部科名</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">役職コード</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">役職名</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">個人コード</th>
            <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">個人名</th>

            @if(count($attendees['segmentType']) > 0)
                @foreach ($attendees['segmentType'] as $segmentType)
                <th style="width:120px;text-align:center;background:#daeef3;border-right:1px solid black;border-bottom:1px solid black;">{{ $segmentType->segment_name }}</th>
                @endforeach
            @endif
            
            @if(count($attendees['targetProduct']) > 0)
                @foreach ($attendees['targetProduct'] as $targetProduct)
                    <th style="width:120px;text-align:center;background:#daeef3;border:1px solid black;border-bottom:1px solid black;">{{ $targetProduct->target_product_name }}</th>
                @endforeach
            @endif

        </tr>
    </thead>
    <tbody>
        @if(count($attendees['datas']) > 0)
            @foreach ($attendees['datas'] as $datas)
                <tr>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->facility_cd }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->facility_name }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->department_cd }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->department_name }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->display_position_cd }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->display_position_name }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->person_cd }}</td>
                    <td style="border:1px solid black;float:left;text-align:left;">{{ $datas->person_name }}</td>

                    @if(count($attendees['segmentType']) > 0)
                        @foreach ($attendees['segmentType'] as $segmentType)
                            <?php $i = 0 ?>
                            @if(count($datas->segment_list) > 0)
                                @foreach($datas->segment_list as $item )
                                    @if( $item->segment_type ==  $segmentType->segment_type)
                                        <td style="text-align:center;border:1px solid black;">〇</td>
                                        <?php $i += 1 ?>
                                        @break
                                    @else
                                        @if($loop->last)
                                            <td style="border:1px solid black;"></td>
                                            <?php $i += 1 ?>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                            @if($i == 0)
                                <td style="border:1px solid black;"></td>
                            @endif
                        @endforeach
                    @endif

                    @if(count($attendees['targetProduct']) > 0)
                        @foreach ($attendees['targetProduct'] as $targetProduct)
                            <?php $j = 0 ?>
                            @if(count($datas->target_product_list) > 0)
                                @foreach($datas->target_product_list as $item_target )
                                    @if( $item_target->target_product_cd ==  $targetProduct->target_product_cd)
                                        <td style="text-align:center;border:1px solid black;">{{ $item_target->sub_target }}〇</td>
                                        <?php $j += 1 ?>
                                        @break
                                    @else
                                        @if($loop->last)
                                            <td style="border:1px solid black;"></td>
                                            <?php $j += 1 ?>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                            @if($j == 0)
                                <td style="border:1px solid black;"></td>
                            @endif
                        @endforeach
                    @endif
                </tr>
            @endforeach
        @endif
        
    </tbody>
</table>

