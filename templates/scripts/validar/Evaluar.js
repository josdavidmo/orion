/*  Autor: Rafael Alberto Moreno Parra
 Sitio Web:  http://darwin.50webs.com
 Correo:  enginelife@hotmail.com
 
 Evaluador de expresiones algebraicas por ejemplo, 57+1.87/84.89-(6.8*e+b+8769-4*b/8+b^2*4^e/f)+5.4-(d/9.6+0.2) con las siguientes funcionalidades:
 1. Ilimitado numero de parantesis
 2. M�s rapido y mas sencillo que el evaluador escrito para el primer libro: "Desarrollo de un evaluador de expresiones algebraicas"
 3. Manejo de 26 variables
 4. Manejo de 12 funciones
 5. Manejo de operadores +, -, *, /, ^ (potencia)
 6. Manejo del menos unario: lo reemplaza con (0-1)# donde # es la operaci�n con mayor relevancia y equivale a la multiplicaci�n
 
 Version: 2.00 [Genera el libro "Desarrollo de un evaluador de expresiones algebraicas. Version 2.0"]
 Fecha: Enero de 2013
 Licencia: LGPL
 
 Algoritmo:
 
 Se toma una expresion como 7*x+sen(12.8/y+9)-5*cos(9-(8.3/5.11^3-4.7)*7.12)+0.445
 Se agregan parantesis de inicio y fin  (7*x+sen(12.8/y+9)-5*cos(9-(8.3/5.11^3-4.7)*7.12)+0.445)
 Se divide en piezas simples   | ( | 7 | * | x | + | sen( | 12.8 | / | y | + | 9 | ) | - | 5 | * | cos( | 9 | - | ( | 8.3 | / | 5.11 | ^ | 3 | - | 4.7 | ) | * | 7.12 | ) | + | 0.445 | ) |
 Esas piezas estan clasificadas:
 Parentesis que abre (
 Parentesis que cierra )
 N�meros  7   12.8  9   5
 Variables  x  y
 Operadores + - * / ^
 Funciones  sen(  cos(
 Luego se convierte esa expresion larga en expresiones cortas de ejecucion del tipo
 Acumula = operando(numero/variable/acumula)  operador(+, -, *, /, ^)   operando(numero/variable/acumula)
 [0]  5.11 ^ 3
 [1]  8.3  / [0]
 [2]  [1] - 4.7
 [3]  [2] + 0
 [4]  [3] * 7,12
 [5]  9   - [4]
 [6]  cos([5])
 [7]  12,8 / y
 [8]  [7] +  9
 [9]  sen([8])
 [10] 7 * x
 [11] 5 * [6]
 [12] [10] + [9]
 [13] [12] - [11]
 [14] [13] + 0.445
 [15] [14] + 0
 La expresion ya esta analizada y lista para evaluar.
 Se evalua yendo de [0] a [15], en [15] esta el valor final.
 */

function Pieza_Simple()
{
    this.tipo; //Funcion, parentesis_abre, parentesis_cierra, operador, numero, variable, abreviacion
    this.funcion; //Que funcion es seno/coseno/tangente/sqrt
    this.operador; // +, -, *, /, ^
    this.numero; //N�mero real de la expresi�n
    this.variableAlgebra; //Variable de la expresi�n
    this.acumula;  //Indice de la microexpresi�n

    this.getTipo = function()
    {
        return this.tipo;
    }

    this.getFuncion = function()
    {
        return this.funcion;
    }

    this.getOperador = function()
    {
        return this.operador;
    }

    this.getNumero = function()
    {
        return this.numero;
    }

    this.getVariable = function()
    {
        return this.variableAlgebra;
    }

    this.getAcumula = function()
    {
        return this.acumula;
    }

    this.setAcumula = function(acumula)
    {
        this.tipo = 7;
        this.acumula = acumula;
    }

    this.ConstructorPieza_Simple = function(tipo, funcion, operador, numero, variable)
    {
        this.tipo = tipo;
        this.funcion = funcion;
        this.operador = operador;
        this.variableAlgebra = variable;
        this.acumula = 0;
        this.numero = numero;
    }
}

function Pieza_Ejecuta()
{
    this.valorPieza;

    this.funcion;

    this.tipo_operandoA;
    this.numeroA;
    this.variableA;
    this.acumulaA;

    this.operador;

    this.tipo_operandoB;
    this.numeroB;
    this.variableB;
    this.acumulaB;

    this.getValorPieza = function()
    {
        return this.valorPieza;
    }

    this.setValorPieza = function(valor)
    {
        this.valorPieza = valor;
    }

    this.getFuncion = function()
    {
        return this.funcion;
    }

    this.getTipoOperA = function()
    {
        return this.tipo_operandoA;
    }

    this.getNumeroA = function()
    {
        return this.numeroA;
    }

    this.getVariableA = function()
    {
        return this.variableA;
    }

    this.getAcumulaA = function()
    {
        return this.acumulaA;
    }

    this.getOperador = function()
    {
        return this.operador;
    }

    this.getTipoOperB = function()
    {
        return this.tipo_operandoB;
    }

    this.getNumeroB = function()
    {
        return this.numeroB;
    }

    this.getVariableB = function()
    {
        return this.variableB;
    }

    this.getAcumulaB = function()
    {
        return this.acumulaB;
    }

    this.ConstructorPieza_Ejecuta = function(funcion, tipo_operandoA, numeroA, variableA, acumulaA, operador, tipo_operandoB, numeroB, variableB, acumulaB)
    {
        this.valorPieza = 0;

        this.funcion = funcion;

        this.tipo_operandoA = tipo_operandoA;
        this.numeroA = numeroA;
        this.variableA = variableA;
        this.acumulaA = acumulaA;

        this.operador = operador;

        this.tipo_operandoB = tipo_operandoB;
        this.numeroB = numeroB;
        this.variableB = variableB;
        this.acumulaB = acumulaB;
    }
}

function Evaluar()
{
    /* Esta constante sirve para que se reste al car�cter y se obtenga el n�mero.  Ejemplo:  '7' - ASCIINUMERO =  7 */
    this.ASCIINUMERO = 48;

    /* Esta constante sirve para que se reste al car�cter y se obtenga el n�mero de la letra. Ejemplo:  'b' - ASCIILETRA =  1 */
    this.ASCIILETRA = 97;

    /* Las funciones que soporta este evaluador */
    this.TAMANOFUNCION = 39;
    this.listaFunciones = "sinsencostanabsasnacsatnlogceiexpsqrrcb";

    /* Constantes de los diferentes tipos de datos que tendr�n las piezas */
    this.ESFUNCION = 1;
    this.ESPARABRE = 2;
    this.ESPARCIERRA = 3;
    this.ESOPERADOR = 4;
    this.ESNUMERO = 5;
    this.ESVARIABLE = 6;

    //Listado de Piezas de an�lisis
    this.PiezaSimple = [];

    //Listado de Piezas de ejecuci�n
    this.PiezaEjecuta = [];
    this.Contador_Acumula = 0;

    //Almacena los valores de las 26 diferentes variables que puede tener la expresi�n algebraica
    this.VariableAlgebra = [];

    //Valida la expresi�n algebraica
    this.EvaluaSintaxis = function(expresion)
    {
        //Hace 25 pruebas de sintaxis
        if (this.DobleTripleOperadorSeguido(expresion))
            return 1;
        if (this.OperadorParentesisCierra(expresion))
            return 2;
        if (this.ParentesisAbreOperador(expresion))
            return 3;
        if (this.ParentesisDesbalanceados(expresion))
            return 4;
        if (this.ParentesisVacio(expresion))
            return 5;
        if (this.ParentesisBalanceIncorrecto(expresion))
            return 6;
        if (this.ParentesisCierraNumero(expresion))
            return 7;
        if (this.NumeroParentesisAbre(expresion))
            return 8;
        if (this.DoblePuntoNumero(expresion))
            return 9;
        if (this.ParentesisCierraVariable(expresion))
            return 10;
        if (this.VariableluegoPunto(expresion))
            return 11;
        if (this.PuntoluegoVariable(expresion))
            return 12;
        if (this.NumeroAntesVariable(expresion))
            return 13;
        if (this.VariableDespuesNumero(expresion))
            return 14;
        if (this.Chequea4letras(expresion))
            return 15;
        if (this.FuncionInvalida(expresion))
            return 16;
        if (this.VariableInvalida(expresion))
            return 17;
        if (this.VariableParentesisAbre(expresion))
            return 18;
        if (this.ParCierraParAbre(expresion))
            return 19;
        if (this.OperadorPunto(expresion))
            return 20;
        if (this.ParAbrePunto(expresion))
            return 21;
        if (this.PuntoParAbre(expresion))
            return 22;
        if (this.ParCierraPunto(expresion))
            return 23;
        if (this.PuntoOperador(expresion))
            return 24;
        if (this.PuntoParCierra(expresion))
            return 25;

        return 0; //No se detect� error de sintaxis
    }

    //Muestra mensaje de error sint�ctico
    this.MensajeSintaxis = function(CodigoError)
    {
        switch (CodigoError)
        {
            case 0:
                return "No se detecta error sintactico en las 25 pruebas que se hicieron.";
            case 1:
                return "1. Dos o mas operadores estan seguidos. Ejemplo: 2++4, 5-*3";
            case 2:
                return "2. Un operador seguido de un parantesis que cierra. Ejemplo: 2-(4+)-7";
            case 3:
                return "3. Un parentesis que abre seguido de un operador. Ejemplo: 2-(*3)";
            case 4:
                return "4. Que los parentesis estan desbalanceados. Ejemplo: 3-(2*4))";
            case 5:
                return "5. Que haya parentesis vacio. Ejemplo: 2-()*3";
            case 6:
                return "6. Parentesis que abre no corresponde con el que cierra. Ejemplo: 2+3)-2*(4";
            case 7:
                return "7. Un parentesis que cierra y sigue un numero. Ejemplo: (3-5)7-(1+2)";
            case 8:
                return "8. Un numero seguido de un parentesis que abre. Ejemplo: 7-2(5-6)";
            case 9:
                return "9. Doble punto en un numero de tipo real. Ejemplo: 3-2..4+1  7-6.46.1+2";
            case 10:
                return "10. Un parentesis que cierra seguido de una variable. Ejemplo: (12-4)y-1";
            case 11:
                return "11. Una variable seguida de un punto. Ejemplo: 4-z.1+3";
            case 12:
                return "12. Un punto seguido de una variable. Ejemplo: 7-2.p+1";
            case 13:
                return "13. Un numero antes de una variable. Ejemplo: 3x+1";
            case 14:
                return "14. Un numero despues de una variable. Ejemplo: x21+4";
            case 15:
                return "15. Hay 4 o mas letras seguidas. Ejemplo: 12+ramp+8.9";
            case 16:
                return "16. Funcion inexistente. Ejemplo: 5*alo(78)";
            case 17:
                return "17. Variable invalida (solo pueden tener una letra). Ejemplo: 5+tr-xc+5";
            case 18:
                return "18. Variable seguida de parentesis que abre. Ejemplo: 5-a(7+3)";
            case 19:
                return "19. Despues de parentesis que cierra sigue parentesis que abre. Ejemplo: (4-5)(2*x)";
            case 20:
                return "20. Despues de operador sigue un punto. Ejemplo: -.3+7";
            case 21:
                return "21. Despues de parentesis que abre sigue un punto. Ejemplo: 3*(.5+4)";
            case 22:
                return "22. Un punto seguido de un parentesis que abre. Ejemplo: 7+3.(2+6)";
            case 23:
                return "23. Parentesis cierra y sigue punto. Ejemplo: (4+5).7-2";
            case 24:
                return "24. Punto seguido de operador. Ejemplo: 5.*9+1";
            default:
                return "25. Punto seguido de parentesis que cierra. Ejemplo: (3+2.)*5";
        }
    }

    //Retira caracteres inv�lidos. Pone la expresi�n entre par�ntesis.
    this.TransformaExpresion = function(expr)
    {
        var validos = "abcdefghijklmnopqrstuvwxyz0123456789.+-*/^()";
        var expr2 = expr.toLowerCase();
        var nuevaExpr = "(";
        for (var pos = 0; pos < expr2.length; pos++)
        {
            var letra = expr2.charAt(pos);
            for (var valida = 0; valida < validos.length; valida++)
                if (letra == validos.charAt(valida))
                {
                    nuevaExpr += letra;
                    break;
                }
        }
        nuevaExpr += ')';
        return nuevaExpr;
    }

    //1. Dos o m�s operadores est�n seguidos. Ejemplo: 2++4, 5-*3
    this.DobleTripleOperadorSeguido = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
        {
            var car1 = expr.charAt(pos);   //Extrae un car�cter
            var car2 = expr.charAt(pos + 1); //Extrae el siguiente car�cter

            //Compara si el car�cter y el siguiente son operadores, dado el caso retorna true
            if (car1 == '+' || car1 == '-' || car1 == '*' || car1 == '/' || car1 == '^')
                if (car2 == '+' || car2 == '*' || car2 == '/' || car2 == '^')
                    return true;
        }

        for (var pos = 0; pos < expr.length - 2; pos++)
        {
            var car1 = expr.charAt(pos);   //Extrae un car�cter
            var car2 = expr.charAt(pos + 1); //Extrae el siguiente car�cter
            var car3 = expr.charAt(pos + 2); //Extrae el siguiente car�cter

            //Compara si el car�cter y el siguiente son operadores, dado el caso retorna true
            if (car1 == '+' || car1 == '-' || car1 == '*' || car1 == '/' || car1 == '^')
                if (car2 == '+' || car2 == '-' || car2 == '*' || car2 == '/' || car2 == '^')
                    if (car3 == '+' || car3 == '-' || car3 == '*' || car3 == '/' || car3 == '^')
                        return true;
        }

        return false;  //No encontr� doble/triple operador seguido
    }

    //2. Un operador seguido de un par�ntesis que cierra. Ejemplo: 2-(4+)-7
    this.OperadorParentesisCierra = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
        {
            var car1 = expr.charAt(pos);   //Extrae un car�cter

            //Compara si el primer car�cter es operador y el siguiente es par�ntesis que cierra
            if (car1 == '+' || car1 == '-' || car1 == '*' || car1 == '/' || car1 == '^')
                if (expr.charAt(pos + 1) == ')')
                    return true;
        }
        return false; //No encontr� operador seguido de un par�ntesis que cierra
    }

    //3. Un par�ntesis que abre seguido de un operador. Ejemplo: 2-(*3)
    this.ParentesisAbreOperador = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
        {
            var car2 = expr.charAt(pos + 1); //Extrae el siguiente car�cter

            //Compara si el primer car�cter es par�ntesis que abre y el siguiente es operador
            if (expr.charAt(pos) == '(')
                if (car2 == '+' || car2 == '*' || car2 == '/' || car2 == '^')
                    return true;
        }
        return false;  //No encontr� par�ntesis que abre seguido de un operador
    }

    //4. Que los par�ntesis est�n desbalanceados. Ejemplo: 3-(2*4))
    this.ParentesisDesbalanceados = function(expr)
    {
        var parabre = 0, parcierra = 0;
        for (var pos = 0; pos < expr.length; pos++)
        {
            var car1 = expr.charAt(pos);
            if (car1 == '(')
                parabre++;
            if (car1 == ')')
                parcierra++;
        }
        return parabre != parcierra;
    }

    //5. Que haya par�ntesis vac�o. Ejemplo: 2-()*3
    this.ParentesisVacio = function(expr)
    {
        //Compara si el primer car�cter es par�ntesis que abre y el siguiente es par�ntesis que cierra
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '(' && expr.charAt(pos + 1) == ')')
                return true;
        return false;
    }

    //6. As� est�n balanceados los par�ntesis no corresponde el que abre con el que cierra. Ejemplo: 2+3)-2*(4
    this.ParentesisBalanceIncorrecto = function(expr)
    {
        var balance = 0;
        for (var pos = 0; pos < expr.length; pos++)
        {
            var car1 = expr.charAt(pos);   //Extrae un car�cter
            if (car1 == '(')
                balance++;
            if (car1 == ')')
                balance--;
            if (balance < 0)
                return true; //Si cae por debajo de cero es que el balance es err�neo
        }
        return false;
    }

    //7. Un par�ntesis que cierra y sigue un n�mero o par�ntesis que abre. Ejemplo: (3-5)7-(1+2)(3/6)
    this.ParentesisCierraNumero = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
        {
            var car2 = expr.charAt(pos + 1); //Extrae el siguiente car�cter

            //Compara si el primer car�cter es par�ntesis que cierra y el siguiente es n�mero
            if (expr.charAt(pos) == ')')
                if (car2 >= '0' && car2 <= '9')
                    return true;
        }
        return false;
    }

    //8. Un n�mero seguido de un par�ntesis que abre. Ejemplo: 7-2(5-6)
    this.NumeroParentesisAbre = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
        {
            var car1 = expr.charAt(pos);   //Extrae un car�cter

            //Compara si el primer car�cter es n�mero y el siguiente es par�ntesis que abre
            if (car1 >= '0' && car1 <= '9')
                if (expr.charAt(pos + 1) == '(')
                    return true;
        }
        return false;
    }

    //9. Doble punto en un n�mero de tipo real. Ejemplo: 3-2..4+1  7-6.46.1+2
    this.DoblePuntoNumero = function(expr)
    {
        var totalpuntos = 0;
        for (var pos = 0; pos < expr.length; pos++)
        {
            var car1 = expr.charAt(pos);   //Extrae un car�cter
            if ((car1 < '0' || car1 > '9') && car1 != '.')
                totalpuntos = 0;
            if (car1 == '.')
                totalpuntos++;
            if (totalpuntos > 1)
                return true;
        }
        return false;
    }

    //10. Un par�ntesis que cierra seguido de una variable. Ejemplo: (12-4)y-1
    this.ParentesisCierraVariable = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == ')') //Compara si el primer car�cter es par�ntesis que cierra y el siguiente es letra
                if (expr.charAt(pos + 1) >= 'a' && expr.charAt(pos + 1) <= 'z')
                    return true;
        return false;
    }

    //11. Una variable seguida de un punto. Ejemplo: 4-z.1+3
    this.VariableluegoPunto = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) >= 'a' && expr.charAt(pos) <= 'z')
                if (expr.charAt(pos + 1) == '.')
                    return true;
        return false;
    }

    //12. Un punto seguido de una variable. Ejemplo: 7-2.p+1
    this.PuntoluegoVariable = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '.')
                if (expr.charAt(pos + 1) >= 'a' && expr.charAt(pos + 1) <= 'z')
                    return true;
        return false;
    }

    //13. Un n�mero antes de una variable. Ejemplo: 3x+1
    //Nota: Algebraicamente es aceptable 3x+1 pero entonces vuelve m�s complejo un evaluador porque debe saber que 3x+1 es en realidad 3*x+1
    this.NumeroAntesVariable = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) >= '0' && expr.charAt(pos) <= '9')
                if (expr.charAt(pos + 1) >= 'a' && expr.charAt(pos + 1) <= 'z')
                    return true;
        return false;
    }

    //14. Un n�mero despu�s de una variable. Ejemplo: x21+4
    this.VariableDespuesNumero = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) >= 'a' && expr.charAt(pos) <= 'z')
                if (expr.charAt(pos + 1) >= '0' && expr.charAt(pos + 1) <= '9')
                    return true;
        return false;
    }

    //15. Chequea si hay 4 o m�s variables seguidas
    this.Chequea4letras = function(expr)
    {
        for (var pos = 0; pos < expr.length - 3; pos++)
        {
            var car1 = expr.charAt(pos);
            var car2 = expr.charAt(pos + 1);
            var car3 = expr.charAt(pos + 2);
            var car4 = expr.charAt(pos + 3);

            if (car1 >= 'a' && car1 <= 'z' && car2 >= 'a' && car2 <= 'z' && car3 >= 'a' && car3 <= 'z' && car4 >= 'a' && car4 <= 'z')
                return true;
        }
        return false;
    }

    //16. Si detecta tres letras seguidas y luego un par�ntesis que abre, entonces verifica si es funci�n o no
    this.FuncionInvalida = function(expr)
    {
        for (var pos = 0; pos < expr.length - 2; pos++)
        {
            var car1 = expr.charAt(pos);
            var car2 = expr.charAt(pos + 1);
            var car3 = expr.charAt(pos + 2);

            //Si encuentra tres letras seguidas
            if (car1 >= 'a' && car1 <= 'z' && car2 >= 'a' && car2 <= 'z' && car3 >= 'a' && car3 <= 'z')
            {
                if (pos >= expr.length - 4)
                    return true; //Hay un error porque no sigue par�ntesis
                if (expr.charAt(pos + 3) != '(')
                    return true; //Hay un error porque no hay par�ntesis
                if (this.EsFuncionInvalida(car1, car2, car3))
                    return true;
            }
        }
        return false;
    }

    //Chequea si las tres letras enviadas son una funci�n
    this.EsFuncionInvalida = function(car1, car2, car3)
    {
        var listafunciones = "sinsencostanabsasnacsatnlogceiexpsqrrcb";
        for (var pos = 0; pos <= listafunciones.length - 3; pos += 3)
        {
            var listfunc1 = listafunciones.charAt(pos);
            var listfunc2 = listafunciones.charAt(pos + 1);
            var listfunc3 = listafunciones.charAt(pos + 2);
            if (car1 == listfunc1 && car2 == listfunc2 && car3 == listfunc3)
                return false;
        }
        return true;
    }

    //17. Si detecta s�lo dos letras seguidas es un error
    this.VariableInvalida = function(expr)
    {
        var cuentaletras = 0;
        for (var pos = 0; pos < expr.length; pos++)
        {
            if (expr.charAt(pos) >= 'a' && expr.charAt(pos) <= 'z')
                cuentaletras++;
            else
            {
                if (cuentaletras == 2)
                    return true;
                cuentaletras = 0;
            }
        }
        return cuentaletras == 2;
    }

    //18. Antes de par�ntesis que abre hay una letra
    this.VariableParentesisAbre = function(expr)
    {
        var cuentaletras = 0;
        for (var pos = 0; pos < expr.length; pos++)
        {
            var car1 = expr.charAt(pos);
            if (car1 >= 'a' && car1 <= 'z')
                cuentaletras++;
            else if (car1 == '(' && cuentaletras == 1)
                return true;
            else
                cuentaletras = 0;
        }
        return false;
    }

    //19. Despu�s de par�ntesis que cierra sigue par�ntesis que abre. Ejemplo: (4-5)(2*x)
    this.ParCierraParAbre = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == ')' && expr.charAt(pos + 1) == '(')
                return true;
        return false;
    }

    //20. Despu�s de operador sigue un punto. Ejemplo: -.3+7
    this.OperadorPunto = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '+' || expr.charAt(pos) == '-' || expr.charAt(pos) == '*' || expr.charAt(pos) == '/' || expr.charAt(pos) == '^')
                if (expr.charAt(pos + 1) == '.')
                    return true;
        return false;
    }

    //21. Despu�s de par�ntesis que abre sigue un punto. Ejemplo: 3*(.5+4)
    this.ParAbrePunto = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '(' && expr.charAt(pos + 1) == '.')
                return true;
        return false;
    }

    //22. Un punto seguido de un par�ntesis que abre. Ejemplo: 7+3.(2+6)
    this.PuntoParAbre = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '.' && expr.charAt(pos + 1) == '(')
                return true;
        return false;
    }

    //23. Par�ntesis cierra y sigue punto. Ejemplo: (4+5).7-2
    this.ParCierraPunto = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == ')' && expr.charAt(pos + 1) == '.')
                return true;
        return false;
    }

    //24. Punto seguido de operador. Ejemplo: 5.*9+1 
    this.PuntoOperador = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '.')
                if (expr.charAt(pos + 1) == '+' || expr.charAt(pos + 1) == '-' || expr.charAt(pos + 1) == '*' || expr.charAt(pos + 1) == '/' || expr.charAt(pos + 1) == '^')
                    return true;
        return false;
    }

    //25. Punto y sigue par�ntesis que cierra. Ejemplo: (3+2.)*5
    this.PuntoParCierra = function(expr)
    {
        for (var pos = 0; pos < expr.length - 1; pos++)
            if (expr.charAt(pos) == '.' && expr.charAt(pos + 1) == ')')
                return true;
        return false;
    }

    /* Convierte una expresi�n con el menos unario en una expresi�n valida para el evaluador de expresiones */
    this.ArreglaNegativos = function(expresion)
    {
        var NuevaExpresion = "";
        var NuevaExpresion2 = "";

        //Si detecta un operador y luego un menos, entonces reemplaza el menos con un "(0-1)#"
        for (var pos = 0; pos < expresion.length; pos++)
        {
            var letra1 = expresion.charAt(pos);
            if (letra1 == '+' || letra1 == '-' || letra1 == '*' || letra1 == '/' || letra1 == '^')
                if (expresion.charAt(pos + 1) == '-')
                {
                    NuevaExpresion += letra1 + "(0-1)#";
                    pos++;
                    continue;
                }
            NuevaExpresion += letra1;
        }

        //Si detecta un par�ntesis que abre y luego un menos, entonces reemplaza el menos con un "(0-1)#"
        for (var pos = 0; pos < NuevaExpresion.length; pos++)
        {
            var letra1 = NuevaExpresion.charAt(pos);
            if (letra1 == '(')
                if (NuevaExpresion.charAt(pos + 1) == '-')
                {
                    NuevaExpresion2 += letra1 + "(0-1)#";
                    pos++;
                    continue;
                }
            NuevaExpresion2 += letra1;
        }

        return NuevaExpresion2;
    }


    //Inicializa las listas, convierte la expresi�n en piezas simples y luego en piezas de ejecuci�n
    this.Analizar = function(expresion)
    {
        this.PiezaSimple.length = 0;
        this.PiezaEjecuta.length = 0;
        this.Generar_Piezas_Simples(expresion);
        this.Generar_Piezas_Ejecucion();
        /*var totalPiezaEjecuta = this.PiezaEjecuta.length;
         for (var cont = 0; cont < totalPiezaEjecuta; cont++)
         this.PiezaEjecuta[cont].Imprime(cont);*/
    }

    //Convierte la expresi�n en piezas simples: n�meros # par�ntesis # variables # operadores # funciones
    this.Generar_Piezas_Simples = function(expresion)
    {
        var longExpresion = expresion.length;
        var NumeroPiezaSimple = 0;

        //Variables requeridas para armar un n�mero
        var parteentera = 0;
        var partedecimal = 0;
        var divide = 1;
        var entero = true;
        var armanumero = false;

        for (var cont = 0; cont < longExpresion; cont++) //Va de letra en letra de la expresi�n
        {
            var letra = expresion.charAt(cont);
            if (letra == '.')  //Si letra es . entonces el resto de digitos le�dos son la parte decimal del n�mero
                entero = false;
            else if (letra >= '0' && letra <= '9')  //Si es un n�mero, entonces lo va armando
            {
                armanumero = true;
                if (entero)
                    parteentera = parteentera * 10 + parseFloat(letra); //La parte entera del n�mero 
                else
                {
                    divide *= 10;
                    partedecimal = partedecimal * 10 + parseFloat(letra); //La parte decimal del n�mero
                }
            }
            else
            {
                if (armanumero) //Si ten�a armado un n�mero, entonces crea la pieza ESNUMERO
                {
                    objeto = new Pieza_Simple();
                    objeto.ConstructorPieza_Simple(this.ESNUMERO, 0, '0', parteentera + partedecimal / divide, 0, 0);
                    this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                    parteentera = 0;
                    partedecimal = 0;
                    divide = 1;
                    entero = true;
                    armanumero = false;
                }

                if (letra == '+' || letra == '-' || letra == '*' || letra == '/' || letra == '^' || letra == '#') {
                    objeto = new Pieza_Simple();
                    objeto.ConstructorPieza_Simple(this.ESOPERADOR, 0, letra, 0, 0, 0);
                    this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                }
                else if (letra == '(') {
                    objeto = new Pieza_Simple();
                    objeto.ConstructorPieza_Simple(this.ESPARABRE, 0, '0', 0, 0, 0);
                    this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                } //�Es par�ntesis que abre?
                else if (letra == ')') {
                    objeto = new Pieza_Simple();
                    objeto.ConstructorPieza_Simple(this.ESPARCIERRA, 0, '0', 0, 0, 0);
                    this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                }//�Es par�ntesis que cierra?
                else if (letra >= 'a' && letra <= 'z') //�Es variable o funci�n?
                {
                    /* Detecta si es una funci�n porque tiene dos letras seguidas */
                    if (cont < longExpresion - 1)
                    {
                        letra2 = expresion.charAt(cont + 1); /* Chequea si el siguiente car�cter es una letra, dado el caso es una funci�n */
                        if (letra2 >= 'a' && letra2 <= 'z')
                        {
                            letra3 = expresion.charAt(cont + 2);
                            funcionDetectada = 1;  /* Identifica la funci�n */
                            for (funcion = 0; funcion <= this.TAMANOFUNCION; funcion += 3)
                            {
                                if (letra == this.listaFunciones.charAt(funcion)
                                        && letra2 == this.listaFunciones.charAt(funcion + 1)
                                        && letra3 == this.listaFunciones.charAt(funcion + 2))
                                    break;
                                funcionDetectada++;
                            }
                            objeto = new Pieza_Simple();
                            objeto.ConstructorPieza_Simple(this.ESFUNCION, funcionDetectada, '0', 0, 0, 0);  //Adiciona funci�n a la lista
                            this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                            cont += 3; /* Mueve tres caracteres  sin(  [s][i][n][(] */
                        }
                        else /* Es una variable, no una funci�n */
                        {
                            objeto = new Pieza_Simple();
                            objeto.ConstructorPieza_Simple(this.ESVARIABLE, 0, '0', 0, letra.charCodeAt(0) - this.ASCIILETRA, 0);
                            this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                        }
                    }
                    else /* Es una variable, no una funci�n */
                    {
                        objeto = new Pieza_Simple();
                        objeto.ConstructorPieza_Simple(this.ESVARIABLE, 0, '0', 0, letra.charCodeAt(0) - this.ASCIILETRA, 0);
                        this.PiezaSimple[NumeroPiezaSimple++] = objeto;
                    }
                }
            }
        }
        if (armanumero) {
            objeto = new Pieza_Simple();
            objeto.ConstructorPieza_Simple(this.ESNUMERO, 0, '0', parteentera + partedecimal / divide, 0, 0);
            this.PiezaSimple[NumeroPiezaSimple++] = objeto;
        }
    }

    //Toma las piezas simples y las convierte en piezas de ejecuci�n de funciones
    //Acumula = funci�n (operando(n�mero/variable/acumula))
    this.Generar_Piezas_Ejecucion = function()
    {
        var cont = this.PiezaSimple.length - 1;
        this.Contador_Acumula = 0;
        do
        {
            if (this.PiezaSimple[cont].getTipo() == this.ESPARABRE || this.PiezaSimple[cont].getTipo() == this.ESFUNCION)
            {
                this.Generar_Piezas_Operador("#", "#", cont);  //Primero eval�a las potencias
                this.Generar_Piezas_Operador("^", "^", cont);  //Primero eval�a las potencias
                this.Generar_Piezas_Operador("*", "/", cont);  //Luego eval�a multiplicar y dividir
                this.Generar_Piezas_Operador("+", "-", cont);  //Finalmente eval�a sumar y restar

                //Crea pieza de ejecuci�n
                objeto = new Pieza_Ejecuta();
                objeto.ConstructorPieza_Ejecuta(this.PiezaSimple[cont].getFuncion(),
                        this.PiezaSimple[cont + 1].getTipo(), this.PiezaSimple[cont + 1].getNumero(), this.PiezaSimple[cont + 1].getVariable(), this.PiezaSimple[cont + 1].getAcumula(),
                        '+', this.ESNUMERO, 0, 0, 0);
                this.PiezaEjecuta[this.Contador_Acumula] = objeto;

                //La pieza pasa a ser de tipo Acumulador
                this.PiezaSimple[cont + 1].setAcumula(this.Contador_Acumula++);

                //Quita el par�ntesis/funci�n que abre y el que cierra, dejando el centro
                this.PiezaSimple.splice(cont, 1);
                this.PiezaSimple.splice(cont + 1, 1);
            }
            cont--;
        } while (cont >= 0);
    }

    //Toma las piezas simples y las convierte en piezas de ejecuci�n
    //Acumula = operando(n�mero/variable/acumula)  operador(+, -, *, /, ^)   operando(n�mero/variable/acumula)
    this.Generar_Piezas_Operador = function(operA, operB, inicio)
    {
        var cont = inicio + 1;
        do
        {
            if ((this.PiezaSimple[cont].getTipo() == this.ESOPERADOR) && (this.PiezaSimple[cont].getOperador() == operA || this.PiezaSimple[cont].getOperador() == operB))
            {
                //Crea pieza de ejecuci�n
                objeto = new Pieza_Ejecuta();
                objeto.ConstructorPieza_Ejecuta(0,
                        this.PiezaSimple[cont - 1].getTipo(),
                        this.PiezaSimple[cont - 1].getNumero(), this.PiezaSimple[cont - 1].getVariable(), this.PiezaSimple[cont - 1].getAcumula(),
                        this.PiezaSimple[cont].getOperador(),
                        this.PiezaSimple[cont + 1].getTipo(),
                        this.PiezaSimple[cont + 1].getNumero(), this.PiezaSimple[cont + 1].getVariable(), this.PiezaSimple[cont + 1].getAcumula());
                this.PiezaEjecuta[this.Contador_Acumula] = objeto;

                //Elimina la pieza del operador y la siguiente
                this.PiezaSimple.splice(cont, 1);
                this.PiezaSimple.splice(cont, 1);

                //Retorna el contador en uno para tomar la siguiente operaci�n
                cont--;

                //Cambia la pieza anterior por pieza acumula
                this.PiezaSimple[cont].setAcumula(this.Contador_Acumula++);
            }
            cont++;
        } while (cont < this.PiezaSimple.length && this.PiezaSimple[cont].getTipo() != this.ESPARCIERRA);
    }

    //Calcula la expresi�n convertida en piezas de ejecuci�n
    this.Calcular = function()
    {
        var valorA = 0, valorB = 0;
        var totalPiezaEjecuta = this.PiezaEjecuta.length;

        for (var cont = 0; cont < totalPiezaEjecuta; cont++)
        {
            switch (this.PiezaEjecuta[cont].getTipoOperA())
            {
                case 5:
                    valorA = this.PiezaEjecuta[cont].getNumeroA();
                    break; //�Es un n�mero?
                case 6:
                    valorA = this.VariableAlgebra[this.PiezaEjecuta[cont].getVariableA()];
                    break;  //�Es una variable?
                case 7:
                    valorA = this.PiezaEjecuta[this.PiezaEjecuta[cont].getAcumulaA()].getValorPieza();
                    break; //�Es una expresi�n anterior?
            }
            if (isNaN(valorA) || !isFinite(valorA))
                return valorA;

            switch (this.PiezaEjecuta[cont].getFuncion())
            {
                case 0:
                    switch (this.PiezaEjecuta[cont].getTipoOperB())
                    {
                        case 5:
                            valorB = this.PiezaEjecuta[cont].getNumeroB();
                            break; //�Es un n�mero?
                        case 6:
                            valorB = this.VariableAlgebra[this.PiezaEjecuta[cont].getVariableB()];
                            break;  //�Es una variable?
                        case 7:
                            valorB = this.PiezaEjecuta[this.PiezaEjecuta[cont].getAcumulaB()].getValorPieza();
                            break; //�Es una expresi�n anterior?
                    }
                    if (isNaN(valorB) || !isFinite(valorB))
                        return valorB;

                    switch (this.PiezaEjecuta[cont].getOperador())
                    {
                        case '#':
                            this.PiezaEjecuta[cont].setValorPieza(valorA * valorB);
                            break;
                        case '+':
                            this.PiezaEjecuta[cont].setValorPieza(valorA + valorB);
                            break;
                        case '-':
                            this.PiezaEjecuta[cont].setValorPieza(valorA - valorB);
                            break;
                        case '*':
                            this.PiezaEjecuta[cont].setValorPieza(valorA * valorB);
                            break;
                        case '/':
                            this.PiezaEjecuta[cont].setValorPieza(valorA / valorB);
                            break;
                        case '^':
                            this.PiezaEjecuta[cont].setValorPieza(Math.pow(valorA, valorB));
                            break;
                    }
                    break;
                case 1:
                case 2:
                    this.PiezaEjecuta[cont].setValorPieza(Math.sin(valorA));
                    break;
                case 3:
                    this.PiezaEjecuta[cont].setValorPieza(Math.cos(valorA));
                    break;
                case 4:
                    this.PiezaEjecuta[cont].setValorPieza(Math.tan(valorA));
                    break;
                case 5:
                    this.PiezaEjecuta[cont].setValorPieza(Math.abs(valorA));
                    break;
                case 6:
                    this.PiezaEjecuta[cont].setValorPieza(Math.asin(valorA));
                    break;
                case 7:
                    this.PiezaEjecuta[cont].setValorPieza(Math.acos(valorA));
                    break;
                case 8:
                    this.PiezaEjecuta[cont].setValorPieza(Math.atan(valorA));
                    break;
                case 9:
                    this.PiezaEjecuta[cont].setValorPieza(Math.log(valorA));
                    break;
                case 10:
                    this.PiezaEjecuta[cont].setValorPieza(Math.ceil(valorA));
                    break;
                case 11:
                    this.PiezaEjecuta[cont].setValorPieza(Math.exp(valorA));
                    break;
                case 12:
                    this.PiezaEjecuta[cont].setValorPieza(Math.sqrt(valorA));
                    break;
                case 13:
                    this.PiezaEjecuta[cont].setValorPieza(Math.pow(valorA, 0.333333333333));
                    break;
            }
        }
        return this.PiezaEjecuta[totalPiezaEjecuta - 1].getValorPieza();
    }

    // Da valor a las variables que tendr� la expresi�n algebraica
    this.ValorVariable = function(varAlgebra, valor)
    {
        this.VariableAlgebra[varAlgebra.charCodeAt(0) - this.ASCIILETRA] = valor;
    }
}
