<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Consultorio Jurídico DAE')</title>
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <!--[if mso]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="min-width: 320px;">
        <tr>
            <td align="center" style="padding: 32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    @hasSection('header')
                        @yield('header')
                    @endif
                    @yield('content')
                    @hasSection('footer')
                        @yield('footer')
                    @endif
                </table>
            </td>
        </tr>
    </table>
</body>
</html>