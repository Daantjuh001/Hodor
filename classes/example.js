function jsObj() {

    this.m_public = 'je moer'; //public variable

    this.o_griffins; //public variable



    this.doGriffins = doGriffins; // public method

    this.doSayItOutLoud = doSayItOutLoud; // public method

    this.getPrivateVariable = getPrivateVariable; // public method



    var m_private = 'je var'; // private variable


    function doSayItOutLoud(m_var_fp) { // this method is public because it has a "this." pointer

        this.m_public = m_var_fp;

        if (confirm(m_var_fp + ' is public knowledge. Do you wish to save it?')) {

            setPrivateVariable(m_var_fp);

        }

    }



    function doGriffins() {

        this.o_griffins = new familyObj();

    }



    function getPrivateVariable() { //this method is public because it has a "this." pointer

        alert('The private variable is ' + m_private);

    }



    function setPrivateVariable(m_var_fp) { //this method is private because it has no "this." pointer

        m_private = m_var_fp;

    }

}
