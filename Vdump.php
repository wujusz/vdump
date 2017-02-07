<?php
/**
 * Funkcja dumpujaca przyjmująca wiele argumentów.
 * Jeśli chcemy aby został użyty die, musi zostać użyty string 'die' jako jeden z argumentów.
 * @author Konrad Wojciechowski
 * @version 0.1
 */
namespace Vdump;


class Vdump
{


    private $args;
    private $backtrace;
    private $useFile;
    private $setBacktrace = false;
    private $setDie = false;

    public function __construct()
    {
        $this->args = func_get_args();
        $this->backtrace = debug_backtrace();
        $this->useFile = explode($_SERVER['DOCUMENT_ROOT'], $this->backtrace[0]['file']);

    }


echo '<style type="text/css">


        </style>';


$backtracePrint = '';
foreach ($backtrace as $key => $bt) {
$file = explode($_SERVER['DOCUMENT_ROOT'], $bt['file']);
$backtracePrint .= '<p>';
$argsPrint = '';
if ($key == 0) {
$backtracePrint .= $key . '# in <span>' . $file[1] . '</span>';
} else {
    if (!empty($bt['args'])) {
        foreach ($bt['args'] as $arg) {
            if (gettype($arg) == 'string') {
                if ($arg == 'die') {
                    continue;
                }
                if ($arg == 'backtrace') {
                    continue;
                }
            }

            if (!empty($argsPrint))
                $argsPrint .= ', ';

            $argsPrint .= gettype($arg);
        }
    }

    $backtracePrint .= $key . '# at ' . $bt['class'] . '->' . $bt['function'] . '(' . $argsPrint . ') in <span>' . $file[1] . ' line ' . $bt['line'] . '</span>';
}
$backtracePrint .= '<p>';
}

$str = "<pre class='pre'>";
$str .='<div data-dump="echo" style="font-size: 19;"><b>' . $useFile[1] . "</b>(line <b>" . $backtrace[0]['line'] . "</b>)</div>";

$btPrint = '<div>
                                <input type="radio" id="show" name="group">
                                <input type="radio" id="hide" name="group" checked>
                                <label for="hide" class="hide"></label>
                                <label for="show" class="show"></label>
                                <span id="content">' .
    $backtracePrint
    . '</span>
                        </div>';

if ($args) {
//              $code = file($backtrace[0]['file']);
//              echo "<b>" . htmlspecialchars(trim($code[$backtrace[0]['line'] - 1])) . "</b>\n";
    ob_start();

    $header = '';
    foreach ($args as $arg) {

        if ($arg == 'die') {
            $setDie = true;
            continue;
        }
        if ($arg == 'backtrace') {
            $setBacktrace = true;
            continue;
        }


        $header .= '<div data-type="' . gettype($arg) . '"';
        switch (gettype($arg)) {
            case "boolean":
            case "integer":
                $header .= ' data-dump="json_encode"><p style="border-bottom:1px solid;margin:0;padding:0 0 0 1em;"><b>gettype(' . gettype($arg) . ')</b></p><p>';
                $header .= json_encode($arg);
                break;
            case "string":
                $header .= ' data-dump="echo"><p style="border-bottom:1px solid;margin:0;padding:0 0 0 1em;"><b>gettype(' . gettype($arg) . ')</b></p><p>';
                $header .= $arg;
                break;
            default:
                $header .= ' data-dump="var_dump"';
                if (is_object($arg))
                    $header .= 'data-class="' . get_class($arg) . '"';

                $header .= '><p style="border-bottom:1px solid;margin:0;padding:0 0 0 1em;"><b>gettype(' . gettype($arg) . ')';
                if (is_object($arg))
                    $header .= ' [' . get_class($arg) . ']';

                $header .= '</b></p><p>';
                var_dump($arg);
        }
        $header .= '</p></div>';
    }

    if ($setBacktrace)
        $str .= $btPrint;

    $str .= $header;

    $str .= ob_get_contents();

    ob_end_clean();
    $str = preg_replace('/=>(\s+)/', ' => ', $str);
    $str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
    $str = preg_replace('/}\n(\s+)\[/', "}\n\n" . '$1[', $str);
    $str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);

    $str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
    $str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
    $str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
    $str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
    $str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
    $str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);

    /* echo "<div class='block tiny_text' style='margin-left: 10px'>";
      echo "Sizes: ";
      foreach ($args as $k => $arg) {
      if ($k > 0)
      echo ",";
      echo count($arg);
      }
      echo "</div>"; */
} else {
    $str .= '<div style="margin: 1em 0; font-size: 15;"><h3>No Parameters Found</h3></div>';
}

$str .= "</pre>";

echo $str;

if ($setDie)
    die;
}


}


function vdump(){

}