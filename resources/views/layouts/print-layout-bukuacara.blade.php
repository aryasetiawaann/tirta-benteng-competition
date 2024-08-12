
{{-- INI TEMPLATE BUAT PDF YAAA JANGAN DIHAPUS --}}
<html>
    <head>
        <style>
            /** Define the margins of your page **/
            @page {
                margin: 130px 30px 160px 30px;
            }

            header {
                position: fixed;
                top: -140px;
                left: 0px;
                right: 0px;
                height: 130px;

                /** Extra personal styles **/
                text-align: center;
            }

            header p {
                text-align: right;
                font-size: 12px;
                text-transform: uppercase;
                margin-top: 60px;
                margin-right: 80px;
                margin-bottom: 3px;
            }

            header h1 {
                text-transform: capitalize;
                font-size: 13px;
                width: 350px;
                margin: 0 auto;
            }

            footer {
                position: fixed; 
                bottom: -160px; 
                left: 0px; 
                right: 0px;
                height: 150px; 

                /** Extra personal styles **/
                text-align: center;
            }

            .table-footer{
            margin: 0 auto;
            }

            .table-footer tr td {
                width: 200px;
                text-align: center;
            }

            .table-footer tr img {
                width: 65px;
            }

            main {
                font-size: 12px;
            }

        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
            <p>HY-TEK's MEET MANAGER 8.0 - 5:00 PM 09/08/2024</p>
            <h1>Tirta Benteng Club Fun Swimming 2024 Tangerang - 10/08/2024 Meet Program</h1>
        </header>

        <footer>
            <table class="table-footer">
                <tr>
                    <td>
                        <img src="{{ public_path('assets/img/logo.png') }}" alt="logo">
                    </td>
                    <td>
                        <img src="{{ public_path('assets/img/logo.png') }}" alt="logo">
                    </td>
                    <td>
                        <img src="{{ public_path('assets/img/logo.png') }}" alt="logo">
                    </td>
                </tr>
            </table>
        </footer>

        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p style="page-break-after: always;">
                Content Page 1
            </p>
            <p style="page-break-after: never;">
                Content Page 2
            </p>
        </main>
    </body>
</html>
