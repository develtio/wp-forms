<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo get_bloginfo('name'); ?></title>
        <style type="text/css">
            /* -------------------------------------
				GLOBAL
			------------------------------------- */
            * {
                margin: 0;
                padding: 0;
                font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
                font-size: 100%;
                line-height: 20px;
            }

            img {
                max-width: 100%;
            }

            body {
                -webkit-font-smoothing: antialiased;
                -webkit-text-size-adjust: none;
            }


            /* -------------------------------------
				ELEMENTS
			------------------------------------- */
            a {
                color: #015699;
            }

            .btn {
                font-size: 13px;
                font-weight: 700;
                display: inline-block;
                text-align: center;
                text-decoration: none;
                text-transform: uppercase;
                border: 0;
                padding: 6px 24px;
                line-height: 20px;
                color: #fff;
                background: #2ab9e5;
                -webkit-transition: 0.2s;
                -moz-transition: 0.2s;
                -ms-transition: 0.2s;
                -o-transition: 0.2s;
                transition: 0.2s;
            }

            last {
                margin-bottom: 0;
            }

            first{
                margin-top: 0;
            }

            padding{
                padding: 12px 0;
            }


            /* -------------------------------------
				BODY
			------------------------------------- */
            table#body-wrap {
                width: 100%;
                padding-top: 24px;
                padding-bottom: 24px;
                background: #f6f6f6;
            }

            table#body-wrap #container{
                border: 1px solid #f0f0f0;
                background: #ffffff;
            }


            /* -------------------------------------
				FOOTER
			------------------------------------- */
            table#footer-wrap {
                width: 100%;
                clear: both !important;
            }

            footer-wrap #container p {
                font-size: 12px;
                color: #666;

            }

            table#footer-wrap a{
                color: #999;
            }


            /* -------------------------------------
				TYPOGRAPHY
			------------------------------------- */
            h1,h2,h3{
                font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
                line-height: 24px;
                color: #333;
                margin: 36px 0 24px;
                font-weight: 300;
            }

            h1 {
                font-size: 24px;
                line-height: 32px;
            }
            h2 {
                font-size: 20px;
                line-height: 28px;
            }
            h3 {
                font-size: 16px;
                line-height: 24px;
            }

            p, ul, ol {
                margin-bottom: 24px;
                font-weight: normal;
                font-size: 16px;
            }

            ul li, ol li {
                margin-left: 5px;
                list-style-position: inside;
            }

            /* ---------------------------------------------------
				RESPONSIVENESS
				Nuke it from orbit. It's the only way to be sure.
			------------------------------------------------------ */

            /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
            #container {
                display: block!important;
                max-width: 600px!important;
                margin: 0 auto!important;
                clear: both!important;
            }

            /* Set the padding on the td rather than the div for Outlook compatibility */
            #body-wrap #container{
                padding:20px;
            }

            /* This should also be a block element, so that it will fill 100% of the .container */
            #content {
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
                display: block;
            }

            /* Let's make sure tables in the content area are 100% wide */
            #content table {
                width: 100%;
                margin-bottom: 24px;
            }

            #content td {
                vertical-align: top;
            }

            .mvxxs {
                display: block;
                margin-bottom: 6px;
            }

            #wordwrap {
                -ms-word-break: break-all;

                /* Be VERY careful with this, breaks normal words wh_erever */
                word-break: break-all;

                /* Non standard for webkit */
                word-break: break-word;

                -webkit-hyphens: auto;
                -moz-hyphens: auto;
                hyphens: auto;
            }

        </style>
    </head>

    <body>

        <!-- body -->
        <table id="body-wrap">
            <tr>
                <td></td>
                <td id="container">

                    <!-- content -->
                    <div id="content">
                        <table>
                            <tr>
                                <td style="background: #17ff81; color:#000; padding: 10px 0; text-align: center">
                                    {title}
                                </td>
                            </tr>
                            <tr>
                                <td width="100%" height="20px"></td>
                            </tr>
                            <tr>
                                <td>
									{content}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- /content -->

                </td>
                <td></td>
            </tr>

        </table>
        <!-- /body -->

        <!-- footer -->
        <table id="footer-wrap">
            <tr>
                <td></td>
                <td id="container">
                    <div id="content">
                        <table>
                            <tr>
                                <td align="center">
                                    <p>{footer}</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                </td>
                <td></td>
            </tr>

        </table>
        <!-- /footer -->

    </body>
</html>