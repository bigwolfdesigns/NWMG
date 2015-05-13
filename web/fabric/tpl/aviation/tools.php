<script language="JavaScript">

<!--

function solve(form){

H1 = eval(form.H1.value);

if (form.from.value==1 && form.to.value==3){
	<!-- HRC(HV) -->
   H2=3.149e1+7.96683e-2*H1-3.55432e-5*H1*H1-6.72816e3/H1;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };
   
if (form.from.value==1 && form.to.value==1){
	<!-- HV(HV) -->
   form.H2.value=Math.round(H1,0);
   };

if (form.from.value==1 && form.to.value==2){
	<!-- HRB(HV) -->
   H2=0.99+0.95515*H1-2.98645e-5*H1*H1;
   if (H1 < 210 ){H2=H1};
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 630){form.H2.value = "("+Math.round(H2,0)+")"};
   };
   
if (form.from.value==2 && form.to.value==3){
   	<!-- HRC(HBW) -->
   H2=1.81673e1+1.20388e-1*H1-6.94388e-5*H1*H1-4.88327e3/H1;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==2 && form.to.value==1){
   	<!-- HV(HBW) -->
   H2=95515e4/59729-4e3/59729*Math.pow(57026861620-7466125*H1,1/2);
   if (H1 < 210 ){H2=H1};
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 670){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==2 && form.to.value==2){
   	<!-- HBW(HBW) -->
   form.H2.value=Math.round(H1,0);
   };

if (form.from.value==3 && form.to.value==3){
   	<!-- HRC(HK) -->
   H2=6.43102e1+7.59497e-3*H1+1.13729e-5*H1*H1-1.17515e4/H1;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==3 && form.to.value==1){
   	<!-- HV(HK) -->
   H2=0.70753*H1+1.93765e-3*H1*H1-4.82e-6*Math.pow(H1,3)+4.5e-9*Math.pow(H1,4)-1.245e-12*Math.pow(H1,5);
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 940){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==3 && form.to.value==2){
   	<!-- HBW(HK); eerst HV(HK) daarna HBW(HV) -->
   HV=0.70753*H1+1.93765e-3*H1*H1-4.82e-6*Math.pow(H1,3)+4.5e-9*Math.pow(H1,4)-1.245e-12*Math.pow(H1,5);
   H2=0.99+0.95515*HV-2.98645e-5*HV*HV;
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 630){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==4 && form.to.value==3){
   	<!-- HRC(HRA) -->
   H2=-1.25501e2+2.76747*H1-5.94178e-3*H1*H1;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==4 && form.to.value==1){
   <!-- HV(HRA) -->
   H2=-13.1-124.59*H1+11.54448*H1*H1-0.40952*Math.pow(H1,3)+7.11577e-3*Math.pow(H1,4)-6.06438e-5*Math.pow(H1,5)+2.04752e-7*Math.pow(H1,6);
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 940){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==4 && form.to.value==2){
   <!-- HB(HRA) -->
   H2=-642.6+44.161*H1-0.91205*H1*H1+6.9429e-3*Math.pow(H1,3);
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 940){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==5 && form.to.value==3){
   <!-- HRC(HRB), eerst HRA(HRB) bepalen daarna HRC(HRA) -->
   HRA=111.04-1.3324e-3*Math.pow(5.1352e9-3.7527e7*H1,0.5);
   H2=-1.25501e2+2.76747*HRA-5.94178e-3*HRA*HRA;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==5 && form.to.value==1){
   <!-- HV(HRB), eerst HRA(HRB) bepalen daarna HV(HRA) -->
   HRA=111.04-1.3324e-3*Math.pow(5.1352e9-3.7527e7*H1,0.5);
   H1=HRA
   H2=-13.1-124.59*H1+11.54448*H1*H1-0.40952*Math.pow(H1,3)+7.11577e-3*Math.pow(H1,4)-6.06438e-5*Math.pow(H1,5)+2.04752e-7*Math.pow(H1,6);
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 940){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==5 && form.to.value==2){
   <!-- HBW(HRB) -->
   form.H2.value="error"};

if (form.from.value==6 && form.to.value==3){
   <!-- HRC(HRC) -->
   H2=H1;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==6 && form.to.value==1){
   <!-- HV(HRC) -->
   H2=350.6884-20.7088*H1+1.0768*H1*H1-0.0188*Math.pow(H1,3)+1.3687e-4*Math.pow(H1,4);
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 940){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==6 && form.to.value==2){
   <!-- HBW(HRC) -->
   H2=118.6665+5.9721*H1-0.0719*H1*H1+1.9833e-3*Math.pow(H1,3);
   form.H2.value=Math.round(H2,0);
   if (H2 < 100 || H2 > 630){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==7 && form.to.value==3){
   <!-- HRC(HRD) -->
   H2=-3.20806e1+1.30193*H1;
   form.H2.value=Math.round(H2,0);
   if (H2 < 20 || H2 > 68){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==7 && form.to.value==1){
   <!-- HV(HRD); eerst HRC(HRD) daarna HV(HRC) -->
   HRC=-3.20806e1+1.30193*H1;
   H1=HRC
   H2=350.6884-20.7088*H1+1.0768*H1*H1-0.0188*Math.pow(H1,3)+1.3687e-4*Math.pow(H1,4);
   form.H2.value=Math.round(H2,0);
   if (H2 < 238 || H2 > 940){form.H2.value = "("+Math.round(H2,0)+")"};
   };

if (form.from.value==7 && form.to.value==2){
   <!-- HBW(HRD); eerst HRC(HRD) daarna HBW(HRC) -->
   HRC=-3.20806e1+1.30193*H1;
   H1=HRC
   H2=118.6665+5.9721*H1-0.0719*H1*H1+1.9833e-3*Math.pow(H1,3);
   form.H2.value=Math.round(H2,0);
   if (H2 < 226 || H2 > 630){form.H2.value = "("+Math.round(H2,0)+")"};
   };
   
if (form.from.value==8 && form.to.value==3){
   <!-- HRC(HR15N) -->
   H2=-3.74666e2+1.27582e1*H1-1.48317e-1*H1*H1+6.68816e-4*Math.pow(H1,3);
   form.H2.value=Math.round(H2,0);
   if (H1 < 69.4 || H1 > 93.2) {form.H2.value = "NaN"; alert(" 69.4 < HR15N < 93.2 ")};
   };

if (form.from.value==8 && form.to.value==1){
   <!-- HV(HR15N); eerst HRC(HR15N) daarna HV(HRC) -->
   HRC=-3.74666e2+1.27582e1*H1-1.48317e-1*H1*H1+6.68816e-4*Math.pow(H1,3);
   H2=350.6884-20.7088*HRC+1.0768*HRC*HRC-0.0188*Math.pow(HRC,3)+1.3687e-4*Math.pow(HRC,4);
   form.H2.value=Math.round(H2,0);
   if (H1 < 69.4 || H1 > 93.2) {form.H2.value = "NaN"; alert(" 69.4 < HR15N < 93.2 ")};
   };

if (form.from.value==8 && form.to.value==2){
   <!-- HBW(HR15N); eerst HRC(HR15N) daarna HBW(HRC) -->
   HRC=-3.74666e2+1.27582e1*H1-1.48317e-1*H1*H1+6.68816e-4*Math.pow(H1,3);
   H2=118.6665+5.9721*HRC-0.0719*HRC*HRC+1.9833e-3*Math.pow(HRC,3);
   form.H2.value=Math.round(H2,0);
   if (H1 < 69.4 || H1 > 93.2) {form.H2.value = "NaN"; alert(" 69.4 < HR15N < 93.2 ")};
   };

if (form.from.value==9 && form.to.value==3){
   <!-- HRC(HR30N) -->
   H2=-2.60390e1+1.11079*H1;
   form.H2.value=Math.round(H2,0);
   if (H1 < 41.5 || H1 > 84.4) {form.H2.value = "NaN"; alert(" 41.5 < HR30N < 84.4 ")};
   };
   
if (form.from.value==9 && form.to.value==1){
   <!-- HV(HR30N); eerst HRC(HR30N) daarna HV(HRC) -->
   HRC=-2.60390e1+1.11079*H1;
   H2=350.6884-20.7088*HRC+1.0768*HRC*HRC-0.0188*Math.pow(HRC,3)+1.3687e-4*Math.pow(HRC,4);
   form.H2.value=Math.round(H2,0);
   if (H1 < 41.5 || H1 > 84.4) {form.H2.value = "NaN"; alert(" 41.5 < HR30N < 84.4 ")};
   };

if (form.from.value==9 && form.to.value==2){
   <!-- HBW(HR30N); eerst HRC(HR30N) daarna HBW(HRC) -->
   HRC=-2.60390e1+1.11079*H1;
   H2=118.6665+5.9721*HRC-0.0719*HRC*HRC+1.9833e-3*Math.pow(HRC,3);
   form.H2.value=Math.round(H2,0);
   if (H1 < 41.5 || H1 > 84.4) {form.H2.value = "NaN"; alert(" 41.5 < HR30N < 84.4 ")};
   };

if (form.from.value==10 && form.to.value==3){
   <!-- HRC(HR45N) -->
   H2=3.18978+8.54135e-1*H1;
   form.H2.value=Math.round(H2,0);
   if (H1 < 19.6 || H1 > 75.4) {form.H2.value = "NaN"; alert(" 19.6 < HR45N < 75.4 ")};
   };

if (form.from.value==10 && form.to.value==1){
   <!-- HV(HR45N); eerst HRC(HR45N) daarna HV(HRC) -->
   HRC=3.18978+8.54135e-1*H1;
   H2=350.6884-20.7088*HRC+1.0768*HRC*HRC-0.0188*Math.pow(HRC,3)+1.3687e-4*Math.pow(HRC,4);
   form.H2.value=Math.round(H2,0);
   if (H1 < 19.6 || H1 > 75.4) {form.H2.value = "NaN"; alert(" 19.6 < HR45N < 75.4 ")};
   };

if (form.from.value==10 && form.to.value==2){
   <!-- HBW(HR45N); eerst HRC(HR45N) daarna HBW(HRC) -->
   HRC=3.18978+8.54135e-1*H1;
   H2=118.6665+5.9721*HRC-0.0719*HRC*HRC+1.9833e-3*Math.pow(HRC,3);
   form.H2.value=Math.round(H2,0);
   if (H1 < 19.6 || H1 > 75.4) {form.H2.value = "NaN"; alert(" 19.6 < HR45N < 75.4 ")};
   };

if (form.from.value==11 && form.to.value==3){
   <!-- HRC(HSc) -->
   H2=1.14708e1+9.61667e-1*H1-3.15195e-3*H1*H1-6.97208e2/H1;
   form.H2.value=Math.round(H2,0);
   if (H1 < 34.2 || H1 > 97.3) {form.H2.value = "NaN"; alert(" 34.2 < HSc < 97.3 ")};
   };

if (form.from.value==11 && form.to.value==1){
   <!-- HV(HSc); eerst HRC(HSc) daarna HV(HRC) -->
   HRC=1.14708e1+9.61667e-1*H1-3.15195e-3*H1*H1-6.97208e2/H1;
   H2=350.6884-20.7088*HRC+1.0768*HRC*HRC-0.0188*Math.pow(HRC,3)+1.3687e-4*Math.pow(HRC,4);
   form.H2.value=Math.round(H2,0);
   if (H1 < 34.2 || H1 > 97.3) {form.H2.value = "NaN"; alert(" 34.2 < HSc < 97.3 ")};
   };

if (form.from.value==11 && form.to.value==2){
   <!-- HBW(HSc); eerst HRC(HSc) daarna HBW(HRC) -->
   HRC=1.14708e1+9.61667e-1*H1-3.15195e-3*H1*H1-6.97208e2/H1;
   H2=118.6665+5.9721*HRC-0.0719*HRC*HRC+1.9833e-3*Math.pow(HRC,3);
   form.H2.value=Math.round(H2,0);
   if (H1 < 34.2 || H1 > 97.3) {form.H2.value = "NaN"; alert(" 34.2 < HSc < 97.3 ")};
   };
   
};



function showHelp() {
	alert('1) Standard Hardness Conversion for metals acc. ASTM E 140 - 97, September 1999, Conversion for Non-Austenitic Steels, Table 1. The accuracy of the conversion depends on the accuracy of the provided data and the resulting curve-fits.\n\n2) Indentation hardness is not a single fundamental property but a combination of properties, and varies with the type of test. The modulus of elasticity and the depth of indentation influence conversions. Therefore separate conversion tables are neccessary for different materials.\n\n3) Brinell Hardness numbers in parentheses are outsite the range (HB > 630) This limit is set to avoid errors introduced by the deformation of the ball indenter itself.');
}



// -->

</script>
<script language="JavaScript">
/*			
Author: Rik Tamm-Daniels email: rtd@bu.edu
Addapted for use by Aviation Metals
*/	
    var focusflag = false;
    
    Factor  = new Array(.3462,.3604,.3568,.3568,.3533,.3498,.3462,.3427,.3356,.3392,.3392,.3462,.3462,.3568,.3568,.3604,1.030,1.010,1.132, 1.125,1.121,1.075, 1.072,1.075, 1.012, 1.012,1.037, 1.012, 1.030,1.132, 1.012,.229,.236, .575, .812,.911,.911, 1.084,1.095, 1.144, 1.303,1.339, 1.448, 2.120,2.462,2.466);
    var RoundOff = 5;

function UnitConvertI(value,type){
       
    
        if(type=="cm")
            value = value/2.54;
        if(type=="m")
            value = (value*100)/2.54;
        if(type== "mm")
            value = (value/10)/2.54;
        if(type=="ft")
            value = value*12;
        if(type=="yd")
            value = value*36;
                        

    
return value;
}
function UnitConvertF(value,type){
    
        if(type== "cm")
            value = (value/2.54)/12;
        if(type =="m")
            value = ((value*100)/2.54)/12;
        if(type=="mm")
            value = ((value/10)/2.54)/12;				
        if(type=="in")
            value = value/12;
            value = value*3;
        
    
return value;
}
function CheckNum(value,label){
    var String1 = new String(value);
    var String2 = new String("");
    var String3 = new String("");
    var String4 = new String("");
    var temp;
    var i=0;
    var count=0;
    for(i=0;i<String1.length;i++){
        String2 = new String(parseFloat(String1.charAt(i)));
        if(String1.charAt(i)==".")
            count++;
        
        if((String2.length!=1&&String1.charAt(i)!='.')||count>1){
            alert("You entered an illegal value for the " + label);
            return false;
        }
    }
    return true;
}
function CheckParam(Param1,Param2,Param3,Param4){
    var Form = document.weightcalc.form.options[document.weightcalc.form.selectedIndex].text;
    var valid1 = 0,valid2 = 0,valid3 = 0,valid4 = 0;
    var counter = 0;
    
        if(Form=="Round"||Form=="Square"||Form=="Hexagonal"||Form=="Octagonal"){ 
            if(Param1==""||Param3==""||Param4 == ""){
            alert("You must fill in values for the Diameter And the Length!");
            return false;
            }	
            valid1 = CheckNum(Param1,"Diameter");
            valid3 = CheckNum(Param3,"Length");
            valid4 = CheckNum(Param4,"Number of Pieces");
            if(valid1!=true||valid3!=true||valid4!=true)
                return false;
        }
        if(Form=="Flat"||Form=="Sheet"||Form=="Plate"){
        if(Param1==""||Param3==""||Param4 == ""||Param2==""){
            alert("You must fill in values for the Thickness And the Width And the Length!");
            return false;
            }	
            valid1 = CheckNum(Param1,"Thickness");			
            valid2 = CheckNum(Param2,"Width");
            valid3 = CheckNum(Param3,"Length");
            valid4 = CheckNum(Param4,"Number of Pieces");
            if(valid1!=true||valid3!=true||valid4!=true||valid2!=true)
                return false;

        }
        if(Form=="Tubular"){ 
        if(Param1==""||Param3==""||Param4 == ""||Param2==""){
            alert("You must fill in values for the Outer Diameter And the Wall And the Length!");
            return false;
            }	
            valid1 = CheckNum(Param1,"Outer Diameter");
            valid2 = CheckNum(Param2,"Wall");
            valid3 = CheckNum(Param3,"Length");
            valid4 = CheckNum(Param4,"Number of Pieces");
            if(valid1!=true||valid2!=true||valid3!=true||valid4!=true)
                return false;

        }
        if(Form=="Circular"){ 
        if(Param1==""||Param3==""||Param4 == ""){
            alert("You must fill in values for the Diameter And the Thickness!");
            return false;
            }	
            valid1 = CheckNum(Param1,"Diameter");			
            valid3 = CheckNum(Param3,"Thickness");
            valid4 = CheckNum(Param4,"Number of Pieces");
            if(valid1!=true||valid3!=true||valid4!=true)
                return false;

        }
        if(Form=="Ring"){
        if(Param1==""||Param2==""||Param3==""||Param4 == ""){
            alert("You must fill in values for the Outer Diameter And the Inner Diameter And the Thickness!");
            return false;
            }	
            valid1 = CheckNum(Param1,"Outer Diameter");
            valid2 = CheckNum(Param2,"Inner Diameter");
            valid3 = CheckNum(Param3,"Thickness");
            valid4 = CheckNum(Param4,"Number of Pieces");
            if(valid1!=true||valid2!=true||valid3!=true||valid4!=true)
                return false;

        }


    
    return true;
}
function CalculateWeight(){
    
    var Param = new Array(5);
    var Units = new Array(4);
    var Convert;
    var FormType;
    var Result;
    var Good;
    Param[0] = document.weightcalc.param1.value;
    Param[1] = document.weightcalc.param2.value;
    Param[2] = document.weightcalc.param3.value;
    Param[3] = document.weightcalc.param4.value;
    Good = CheckParam(Param[0],Param[1],Param[2],Param[3]);
    if(!Good)
        return;			


    Units[0] = document.weightcalc.units1.options[document.weightcalc.units1.selectedIndex].text;
    Units[1] = document.weightcalc.units2.options[document.weightcalc.units2.selectedIndex].text;;
    Units[2] = document.weightcalc.units3.options[document.weightcalc.units3.selectedIndex].text;;
    
    Param[0] = UnitConvertI(Param[0],Units[0]);
    Param[1] = UnitConvertI(Param[1],Units[1]);
    Param[2] = UnitConvertF(Param[2],Units[2]);
    
    FormType = document.weightcalc.form.options[document.weightcalc.form.selectedIndex].text;
    if(document.weightcalc.product.selectedIndex>0)
    Convert = Factor[document.weightcalc.product.selectedIndex-1];
    else
    Convert = 1;	
    document.weightcalc.result.rsize = "4";
    
        if(FormType=="Round"){
            Result = new String(2.6729 * Param[0] * Param[0] * Convert * Param[2] * Param[3]);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
        }
        if(FormType=="Square"){
            Result = new String(3.4032 * Param[0] * Param[0] * Convert * Param[2] * Param[3]);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
        }
        if(FormType=="Hexagonal"){
            Result= new String(2.9473 * Param[0] * Param[0] * Convert * Param[2] * Param[3]);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
        }
        if(FormType=="Octagonal"){
            Result = new String(2.8193 * Param[0] * Param[0] * Convert * Param[2] * Param[3]);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
        }
        if(FormType=="Flat"||FormType=="Sheet"||FormType=="Plate"){
            Result = new String(3.4032 * Param[0] * Convert * Param[1] * Param[2] * Param[3]);					document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
        }

        if(FormType=="Tubular"){
            Result = 10.68 * (Param[0] - Param[1]) * Convert * Param[1] * Param[2] * Param[3];
            if(Result<0)
                alert("The Width of the Tube Wall cannot exceed the Outer Diameter!");
            else{
            Result = new String(Result);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
            }
        }
        if(FormType=="Circular"){
            Param[2] = UnitConvertI(Param[2],"ft");
            Result = new String(.22274 * Param[0] * Convert * Param[0] * Param[2] * Param[3]);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
        }
        if(FormType=="Ring"){
            Param[2] = UnitConvertI(Param[2],"ft");
            Result = (.22274 * Param[2] * ((Param[0]*Param[0]) - (Param[1]*Param[1])) * Convert * Param[3]);
            if(Result<0)
                alert("The Inner Diameter cannot exceed the Outer Diameter!");
            else{
            Result = new String(Result);
            document.weightcalc.result.value = Result.substring(0,Result.indexOf(".")+RoundOff);
            }
        }


    
}
function ClearFields(){
	
$(document).ready(function(){
document.weightcalc.param1.value = "";
document.weightcalc.param2.value = "";
document.weightcalc.param3.value = "";
document.weightcalc.param4.value = "1";
document.weightcalc.result.value = "";
});

}
function ChangeLabel(){
	
var formType;
$(document).ready(function(){
formType = document.weightcalc.form.options[document.weightcalc.form.selectedIndex].text;
});


    
if(formType=="Round"){
    document.weightcalc.lbl1.value = "Diameter:";
    document.weightcalc.lbl2.disabled=true;
	document.weightcalc.param2.disabled=true;
    document.weightcalc.lbl2.value="";
    document.weightcalc.lbl3.value = "Length:";
    }
if(formType=="Square"){
    document.weightcalc.lbl1.value = "Diameter:";
    document.weightcalc.lbl2.disabled=true;
	document.weightcalc.param2.disabled=true;
    document.weightcalc.lbl2.value="";
    document.weightcalc.lbl3.value = "Length:";
    }
if(formType== "Hexagonal"){
    document.weightcalc.lbl1.value = "Diameter:";
    document.weightcalc.lbl2.disabled=true;
    document.weightcalc.lbl2.value="";
	document.weightcalc.param2.disabled=true;

    document.weightcalc.lbl3.value = "Length:";
    }
if(formType=="Octagonal"){
    document.weightcalc.lbl1.value = "Diameter:";
    document.weightcalc.lbl2.disabled=true;
    document.weightcalc.lbl2.value="";
		document.weightcalc.param2.disabled=true;

    document.weightcalc.lbl3.value = "Length:";
    }
if(formType=="Flat"||formType=="Sheet"||formType=="Plate"){
    document.weightcalc.lbl1.value = "Thickness:";
    document.weightcalc.lbl2.value = "Width:";
	 document.weightcalc.lbl2.disabled=false;
	 	document.weightcalc.param2.disabled=false;


    document.weightcalc.lbl3.value = "Length:";
    }
if(formType=="Tubular"){
    document.weightcalc.lbl1.value = "Outer Diameter:";
    document.weightcalc.lbl2.value = "Wall:";
	document.weightcalc.lbl2.disabled=false;
		document.weightcalc.param2.disabled=false;

    document.weightcalc.lbl3.value = "Length:";
    }
if(formType=="Circular"){
    document.weightcalc.lbl1.value = "Diameter:";
    document.weightcalc.lbl2.disabled=true;
    document.weightcalc.lbl2.value="";
		document.weightcalc.param2.disabled=true;

    document.weightcalc.lbl3.value = "Thickness:";
    }
if(formType=="Ring"){
    document.weightcalc.lbl1.value = "Outer Diameter:";
	 document.weightcalc.lbl2.disabled=false;
	 	document.weightcalc.param2.disabled=false;

    document.weightcalc.lbl2.value = "Inner Diameter:";
    document.weightcalc.lbl3.value = "Thickness:";
    }


}
ChangeLabel();
ClearFields();
</script>
<div class='tools col-md-12'>
	<table style="padding-top:2%" width="750" align="center"cellspacing="5" cellpadding="5">
		<tr>
			<td class="bottombox" valign="top" align="left">
				<form>
					<div class="Hardnessform">
						<strong>Hardness Conversion Calculator</strong>
						<table border="0" width="100%" cellpadding="0" cellspacing="1">
							<tr>
								<td>Convert:</td><td align="right"> 
									<input TYPE="text" NAME="H1" style="width:80px;" VALUE="560"></td>
							</tr>
							<tr>
								<td>From: </td><td align="right">
									<select size="1" name="from" style="width:205px;">
										<option selected value="1" style="width:auto;" title="HV: Vickers Hardness">HV: Vickers Hardness</option>
										<option value="2" style="width:auto;" title="HB: Brinell Hardness 10 mm C-ball 3000 kgf">HB: Brinell Hardness 10 mm C-ball 3000 kgf</option>
										<option value="3" style="width:auto;" title="HK: Knoop Hardness 500 gf and over">HK: Knoop Hardness 500 gf and over</option>
										<option value="4" style="width:auto;" title="HRA: Rockwell A Hardness 60 kgf.">HRA: Rockwell A Hardness 60 kgf.</option>
										<option value="5" style="width:auto;" title="HRB: Rockwell B Hardness 100 kgf.">HRB: Rockwell B Hardness 100 kgf.</option>
										<option value="6" style="width:auto;" title="HRC: Rockwell C Hardness 150 kgf.">HRC: Rockwell C Hardness 150 kgf.</option>

										<option value="7" style="width:auto;" title="HRD: Rockwell D Hardness 100 kgf.">HRD: Rockwell D Hardness 100 kgf.</option>
										<option value="8" style="width:auto;" title="HR15N: Rockwell Superficial 15 kgf.">HR15N: Rockwell Superficial 15 kgf.</option>
										<option value="9" style="width:auto;" title="HR30N: Rockwell Superficial 30 kgf.">HR30N: Rockwell Superficial 30 kgf.</option>
										<option value="10" style="width:auto;" title="HR45N: Rockwell Superficial 45 kgf.">HR45N: Rockwell Superficial 45 kgf.</option>
										<option value="11" style="width:auto;" title="HSc: Scleroscope Hardness">HSc: Scleroscope Hardness</option>
									</select></td>
							</tr>
							<tr>
								<td>To: </td><td align="right">
									<select size="1" name="to" style="width:205px;">
										<option value="1" selected title="HV: Vickers Hardness">HV: Vickers Hardness</option>
										<option value="2" title="HB: Brinell Hardness 10 mm C-Ball 3000 kgf">HB: Brinell Hardness 10 mm C-Ball 3000 kgf</option>
										<option value="3" title="HRC: Rockwell C Hardness 150 kgf.">HRC: Rockwell C Hardness 150 kgf.</option>
									</select></td>
							</tr>

							<tr>
								<td>Answer:</td><td align="right"> <input TYPE="text" NAME="H2" style="width:80px;"></td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input type="button" value="Help" onClick="showHe lp();">
									&nbsp;&nbsp;
									<input type="button" value="Calculate" onClick="solve(form)">
								</td>
							</tr>
						</table>
					</div>
				</form>



			</td>
			<td class="bottombox" valign="top" align="left">


				<form name="weightcalc" method="post" onSubmit="CalculateWeight(); return false;">
					<strong>Metal Weight
						Calculator</strong>
					<table border="0" width="100%" cellpadding="0" cellspacing="1">
						<tbody><tr>
								<td valign="top">

									<table width="100%" border="0" cellpadding="0" cellspacing="0"><tbody>
											<tr>
												<td valign="top"><strong>Alloy:</strong></td>
												<td align="right">
													<select name="product" style="width:180px;">
														<option>Steel (default)</option>
														<option>Aluminum 1100</option>
														<option>Aluminum 2011</option>
														<option>Aluminum 2014</option>
														<option>Aluminum 2017</option>
														<option>Aluminum 2024</option>
														<option>Aluminum 3003</option>
														<option>Aluminum 5005</option>
														<option>Aluminum 5052</option>
														<option>Aluminum 5056</option>
														<option>Aluminum 5083</option>
														<option>Aluminum 5086</option>
														<option>Aluminum 6061</option>
														<option>Aluminum 6063</option>
														<option>Aluminum 7050</option>
														<option>Aluminum 7075</option>
														<option>Aluminum 7178</option>
														<option>Stainless 300 Series</option>
														<option>Stainless 400 Series</option>
														<option>Nickel 200</option>
														<option>Nickel 400</option>
														<option>Nickel R-405</option>
														<option>Nickel K-500</option>
														<option>Nickel 600</option>
														<option>Nickel 625</option>
														<option>Nickel 800H</option>
														<option>Nickel 800AT</option>
														<option>Nickel 825</option>
														<option>Nickel 330</option>
														<option>Nickel 20</option>
														<option>Nickel C-276</option>
														<option>Nickel 2545MD</option>
														<option>Magnesium</option>
														<option>Beryllium</option>
														<option>Titanium</option>
														<option>Zirconium</option>
														<option>Cast Iron</option>
														<option>Zinc</option>
														<option>Brass</option>
														<option>Columbium</option>
														<option>Copper</option>
														<option>Molybdenum</option>
														<option>Silver</option>
														<option>Lead</option>
														<option>Tantalum</option>
														<option>Tungsten</option>
														<option>Gold</option>
													</select>
												</td></tr>
											<tr>
												<td><strong>Shape:</strong></td>
												<td align="right"><select name="form" onChange="ChangeLabel(); ClearFields();" style="width:180px;">
														<option>Round</option>
														<option>Square</option>
														<option>Hexagonal</option>
														<option>Octagonal</option>
														<option>Sheet</option>
														<option>Plate</option>
														<option>Flat</option>
														<option>Tubular</option>
														<option>Circular</option>
														<option>Ring</option>
													</select>
												</td>
											</tr>
											<tr>
												<td><strong>Pieces:</strong></td>
												<td align="right"><input name="param4"  style="width:50px;" type="text"></td>
											</tr>
										</tbody></table>
								</td></tr>
							<tr>
								<td align="center">
									<input name="lbl1" style="width:90px;"  onFocus="if(navigator.appName=='Netscape'&amp; &amp; parseInt(navigator.appVersion)==3); else document.weightcalc.param1.focus(); " onBlur="ChangeLab el();" type="text"><input name="param1" style="width:40px;" type="text"><select name="units1" style="width:50px;">
										<option>in</option>
										<option>ft</option>
										<option>yd</option>
										<option>mm</option>
										<option>cm</option>
										<option>m</option>
									</select>
								</td></tr>
							<tr><td align="center"><input name="lbl2" onFocus="if(!(navigator.appName=='Netscape'&amp; &amp; parseInt(navigator.appVersion)==3)){	if(document.weightcalc.lbl2.value=='(no value)')  document.weightcalc.param3.focus(); else document.weightcalc.param2.focus(); }" type="text" style="width:90px;"><input name="param2" type="text" style="width:40px;"><select name="units2" style="width:50px;">
										<option>in</option>
										<option>ft</option>
										<option>yd</option>
										<option>mm</option>
										<option>cm</option>
										<option>m</option>
									</select>
								</td></tr>
							<tr><td align="center"><input name="lbl3" onFocus="if(navigator.appName=='Netscape'&amp; &amp; parseInt(navigator.appVersion)==3); else document.weightcalc.param3.focus(); " onBlur="ChangeLab el();" type="text" style="width:90px;"><input name="param3" size="10" type="text" style="width:40px;"><select name="units3" style="width:50px;">
										<option>in</option>
										<option>ft</option>
										<option>yd</option>
										<option>mm</option>
										<option>cm</option>
										<option>m</option>
									</select>
								</td></tr>




							<tr>
								<td align="center"><strong>Answer (lbs):</strong><br>

									<input maxlength="8" name="result" size="10" onFocus="if(navigator.appName=='Netscape'&amp; &amp; parseInt(navigator.appVersion)==3); else document.weightcalc.Submit.focus(); " onBlur="ChangeLab el();" type="text">
								</td></tr>
							<tr>
								<td align="center"><input name="Submit" value="Calculate" type="submit">&nbsp;&nbsp;
									<input name="reset" value="Reset" onClick="ChangeLabel(); ClearFields(); return false;" type="reset">
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
						</tbody></table>
				</form>
			</td>
		</tr>
	</table>
</div>