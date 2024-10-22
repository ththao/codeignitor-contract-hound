<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html dir="ltr" lang="en-US">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">

    <title>Contract Reminder</title>
    <link class="keep" href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet" type="text/css"><!--[if mso]> <style type="text/css"> body, #body, h1, h2, h3, h4, h5, h6, p, ol, ul, table, td, th { font-family: Arial, Helvetica, sans-serif !important; } </style> <![endif]-->
</head>

<body style="margin: 0px; padding: 0px; text-size-adjust: 100%; background: rgb(244, 247, 251); border-radius: 5px;">
    <table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; padding: 0px; text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51); background: rgb(244, 247, 251); border-radius: 5px; margin: 40px 0px; height: 100% !important; width: 100% !important;">
        <tbody>
            <tr>
                <td style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51);">
                    <table width="600" align="center" id="frame" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51); max-width: 600px; margin: auto;">
                        <tbody>
                            <tr>
                                <td style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51);">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header" id="header" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51);">
                                        <tbody>
                                            <tr>
                                                <td class="header-brand" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51); vertical-align: middle; padding: 10px 0px; width: 260px;"><a class="header-logo" href="https://www.contracthound.com" style="color: rgb(144, 159, 174); text-decoration: none; display: block;"><img width="260" src="https://app.contracthound.com/ui/img/emails/logo.png" style="height: auto; line-height: 100%; outline: none; text-decoration: none; border: 0px none; width: 100%; display: block;"></a></td>

                                                <td class="header-links" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51); vertical-align: middle; padding: 10px 0px; text-align: right;"><a href="<?= site_url(); ?>members/login" style="text-decoration: none; color: rgb(144, 159, 174); margin-left: 8px;">Log in</a></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table width="100%" id="content" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51); background: white;">
                                        <tbody>
                                            <tr>
                                                <td style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51);">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51);">
                                                        <tbody>
                                                            <tr>
                                                                <td class="padding" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51); padding: 30px 8%;">
                                                                    <h3 style="font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; color: rgb(51, 51, 51); padding: 0px; margin: 12px 0px; font-size: 27.648px; line-height: 1.35;">Contract Reminder</h3>

																	<a href="<?= site_url(); ?>contracts/view/<?php echo $oContract->contract_id; ?>" class="block" style="text-decoration: none; display: block; font-style: normal; color: rgb(51, 51, 51);">
																	<?php $iCurrentTime = time(); $i5Days = strtotime('+5 days'); $i21Days = strtotime('+21 days');	$iAlertDate = strtotime($oReminder->alert_date); ?>
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="reminder reminder-<?php if ($iAlertDate < $i5Days): ?>danger<?php elseif ($iAlertDate < $i21Days): ?>warning<?php else: ?>default<?php endif; ?>" style="border-collapse:
	                                                                    	collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; line-height: 1.5; color: rgb(51, 51, 51);
		                                                                    border-style: solid; border-color: white; border-image: initial; border-width: 10px 0px; text-align: left; background: rgb(241, 90, 41); border-radius: 4px;">
                                                                        <tbody style="text-align: left;">
                                                                            <tr style="text-align: left;">
                                                                                <td class="reminder-time" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;
	                                                                                line-height: 1.5; text-align: left; vertical-align: top; padding: 10px 20px; width: 48px; background-color: rgb(231, 50, 22);
		                                                                            color: rgb(255, 255, 255); border-radius: 4px 0px 0px 4px;"><?php //echo date('n/j',strtotime($oReminder->alert_date)); ?><?php echo convertto_local_datetime($oReminder->alert_date,$time_zone,'%x'); ?>
                                                                                </td>

                                                                                <td class="reminder-body" style="border-collapse: collapse; font-family: Lato, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;
	                                                                                line-height: 1.5; text-align: left; vertical-align: top; padding: 10px 20px; background-color: rgb(241, 90, 41); color: rgb(255, 255, 255);
		                                                                            border-radius: 0px 4px 4px 0px;"><strong style="font-weight: bold; font-style: normal; text-align: left;"><?php echud($oContract->name); ?></strong>
			                                                                        <em style="text-align: left;"><?php echud($oReminder->message); ?></em></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
																	</a>
																	<a href="https://www.contracthound.com" class="block" style="text-decoration: none; display: block; font-style: normal; color: rgb(51, 51, 51);"></a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
